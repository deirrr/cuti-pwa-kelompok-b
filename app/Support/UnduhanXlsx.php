<?php

namespace App\Support;

use DateTimeInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\CompressionMethod;
use ZipStream\ZipStream;

class UnduhanXlsx
{
    /**
     * @param  list<string>  $judulKolom
     * @param  iterable<array<int, bool|DateTimeInterface|float|int|string|null>>  $baris
     * @param  list<'date'|'datetime'|'decimal'|'integer'|'text'>  $tipeKolom
     */
    public function download(
        string $namaFile,
        string $judul,
        string $periode,
        array $judulKolom,
        iterable $baris,
        array $tipeKolom,
    ): StreamedResponse {
        return $this->downloadBanyakSheet($namaFile, [[
            'nama' => 'Rekap',
            'judul' => $judul,
            'periode' => $periode,
            'judul_kolom' => $judulKolom,
            'baris' => $baris,
            'tipe_kolom' => $tipeKolom,
        ]]);
    }

    /**
     * @param  list<array{
     *     nama: string,
     *     judul: string,
     *     periode: string,
     *     judul_kolom: list<string>,
     *     baris: iterable<array<int, bool|DateTimeInterface|float|int|string|null>>,
     *     tipe_kolom: list<'date'|'datetime'|'decimal'|'integer'|'text'>
     * }>  $sheet
     */
    public function downloadBanyakSheet(string $namaFile, array $sheet): StreamedResponse
    {
        $sheetDinormalkan = $this->normalkanSheet($sheet);
        $berkas = $this->buatBerkas($sheetDinormalkan);

        return response()->streamDownload(function () use ($berkas): void {
            $keluaran = fopen('php://temp', 'w+b');

            if ($keluaran === false) {
                return;
            }

            $zip = new ZipStream(
                outputStream: $keluaran,
                sendHttpHeaders: false,
                enableZip64: false,
                defaultEnableZeroHeader: false,
            );

            foreach ($berkas as $lokasi => $isi) {
                $zip->addFile(
                    fileName: $lokasi,
                    data: $isi,
                    compressionMethod: CompressionMethod::STORE,
                );
            }

            $zip->finish();
            rewind($keluaran);
            $arsip = stream_get_contents($keluaran);
            fclose($keluaran);

            if ($arsip !== false) {
                echo $this->normalkanVersiPembuatZip($arsip);
            }
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * @param  list<array{
     *     nama: string,
     *     judul: string,
     *     periode: string,
     *     judul_kolom: list<string>,
     *     baris: iterable<array<int, bool|DateTimeInterface|float|int|string|null>>,
     *     tipe_kolom: list<'date'|'datetime'|'decimal'|'integer'|'text'>
     * }>  $sheet
     * @return list<array{
     *     nama: string,
     *     judul: string,
     *     periode: string,
     *     judul_kolom: list<string>,
     *     baris: list<array<int, bool|DateTimeInterface|float|int|string|null>>,
     *     tipe_kolom: list<'date'|'datetime'|'decimal'|'integer'|'text'>
     * }>
     */
    private function normalkanSheet(array $sheet): array
    {
        if ($sheet === []) {
            throw new InvalidArgumentException('Workbook harus memiliki minimal satu sheet.');
        }

        $namaTerpakai = [];

        return collect($sheet)->map(function (array $item) use (&$namaTerpakai): array {
            $nama = trim($item['nama']);
            $kunciNama = mb_strtolower($nama);

            if ($nama === '' || mb_strlen($nama) > 31 || preg_match('/[\\\\\/?*\[\]:]/u', $nama) === 1) {
                throw new InvalidArgumentException("Nama sheet '{$nama}' tidak valid.");
            }

            if (in_array($kunciNama, $namaTerpakai, true)) {
                throw new InvalidArgumentException("Nama sheet '{$nama}' tidak boleh duplikat.");
            }

            if ($item['judul_kolom'] === [] || count($item['judul_kolom']) !== count($item['tipe_kolom'])) {
                throw new InvalidArgumentException("Kolom dan tipe data sheet '{$nama}' tidak sesuai.");
            }

            $namaTerpakai[] = $kunciNama;

            return [
                'nama' => $nama,
                'judul' => $item['judul'],
                'periode' => $item['periode'],
                'judul_kolom' => array_values($item['judul_kolom']),
                'baris' => collect($item['baris'])->map(fn (array $baris): array => array_values($baris))->values()->all(),
                'tipe_kolom' => array_values($item['tipe_kolom']),
            ];
        })->values()->all();
    }

    /**
     * ZipStream 3.2.2 menulis field version-made-by sebagai versi 0.3 pada
     * central directory. Excel menolak nilai tersebut meskipun isi OOXML valid.
     */
    private function normalkanVersiPembuatZip(string $arsip): string
    {
        return str_replace(
            "PK\x01\x02\x03\x06",
            "PK\x01\x02\x0A\x03",
            $arsip,
        );
    }

    /**
     * @param  list<array{
     *     nama: string,
     *     judul: string,
     *     periode: string,
     *     judul_kolom: list<string>,
     *     baris: list<array<int, bool|DateTimeInterface|float|int|string|null>>,
     *     tipe_kolom: list<'date'|'datetime'|'decimal'|'integer'|'text'>
     * }>  $sheet
     * @return array<string, string>
     */
    private function buatBerkas(array $sheet): array
    {
        $dibuatPada = now()->translatedFormat('d F Y H:i').' WIB';
        $berkas = [
            '[Content_Types].xml' => $this->contentTypesXml(count($sheet)),
            '_rels/.rels' => $this->rootRelationshipsXml(),
            'docProps/app.xml' => $this->appPropertiesXml(),
            'docProps/core.xml' => $this->corePropertiesXml($sheet[0]['judul']),
            'xl/workbook.xml' => $this->workbookXml($sheet),
            'xl/_rels/workbook.xml.rels' => $this->workbookRelationshipsXml(count($sheet)),
            'xl/styles.xml' => $this->stylesXml(),
        ];

        foreach ($sheet as $index => $item) {
            $berkas['xl/worksheets/sheet'.($index + 1).'.xml'] = $this->worksheetXml(
                $item['judul'],
                $item['periode'],
                $dibuatPada,
                $item['judul_kolom'],
                $item['baris'],
                $item['tipe_kolom'],
            );
        }

        return $berkas;
    }

    private function contentTypesXml(int $jumlahSheet): string
    {
        $sheet = '';

        for ($index = 1; $index <= $jumlahSheet; $index++) {
            $sheet .= '<Override PartName="/xl/worksheets/sheet'.$index.'.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .$sheet
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/><Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/></Types>';
    }

    private function rootRelationshipsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>
XML;
    }

    private function appPropertiesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"><Application>Medika Cuti</Application><AppVersion>1.0</AppVersion></Properties>
XML;
    }

    private function corePropertiesXml(string $judul): string
    {
        $waktu = now()->utc()->format('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:title>'.$this->escape($judul).'</dc:title><dc:creator>Medika Cuti</dc:creator><cp:lastModifiedBy>Medika Cuti</cp:lastModifiedBy>'
            .'<dcterms:created xsi:type="dcterms:W3CDTF">'.$waktu.'</dcterms:created><dcterms:modified xsi:type="dcterms:W3CDTF">'.$waktu.'</dcterms:modified></cp:coreProperties>';
    }

    /**
     * @param  list<array{
     *     nama: string,
     *     judul: string,
     *     periode: string,
     *     judul_kolom: list<string>,
     *     baris: list<array<int, bool|DateTimeInterface|float|int|string|null>>,
     *     tipe_kolom: list<'date'|'datetime'|'decimal'|'integer'|'text'>
     * }>  $sheet
     */
    private function workbookXml(array $sheet): string
    {
        $daftarSheet = '';
        $namaTerdefinisi = '';

        foreach ($sheet as $index => $item) {
            $sheetId = $index + 1;
            $namaSheet = $this->escape($item['nama']);
            $namaSheetFormula = str_replace("'", "''", $item['nama']);
            $kolomTerakhir = $this->hurufKolom(count($item['judul_kolom']));
            $barisTerakhir = max(4, count($item['baris']) + 4);
            $rentangFilter = "'{$namaSheetFormula}'!\$A\$4:\$".$kolomTerakhir.'$'.$barisTerakhir;

            $daftarSheet .= '<sheet name="'.$namaSheet.'" sheetId="'.$sheetId.'" state="visible" r:id="rId'.$sheetId.'"/>';
            $namaTerdefinisi .= '<definedName name="_xlnm._FilterDatabase" localSheetId="'.$index.'" hidden="1">'.$rentangFilter.'</definedName>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<bookViews><workbookView visibility="visible" minimized="0" showHorizontalScroll="1" showVerticalScroll="1" showSheetTabs="1" firstSheet="0" activeTab="0" autoFilterDateGrouping="1"/></bookViews>'
            .'<sheets>'.$daftarSheet.'</sheets>'
            .'<definedNames>'.$namaTerdefinisi.'</definedNames>'
            .'<calcPr calcId="0" fullCalcOnLoad="1"/></workbook>';
    }

    private function workbookRelationshipsXml(int $jumlahSheet): string
    {
        $relasi = '';

        for ($index = 1; $index <= $jumlahSheet; $index++) {
            $relasi .= '<Relationship Id="rId'.$index.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="/xl/worksheets/sheet'.$index.'.xml"/>';
        }

        $relasi .= '<Relationship Id="rId'.($jumlahSheet + 1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$relasi.'</Relationships>';
    }

    private function stylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><numFmts count="4"><numFmt numFmtId="164" formatCode="dd/mm/yyyy"/><numFmt numFmtId="165" formatCode="dd/mm/yyyy hh:mm"/><numFmt numFmtId="166" formatCode="#,##0"/><numFmt numFmtId="167" formatCode="#,##0.0"/></numFmts><fonts count="4"><font><sz val="10"/><name val="Aptos"/><family val="2"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="16"/><name val="Aptos Display"/><family val="2"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="10"/><name val="Aptos"/><family val="2"/></font><font><color rgb="FF475569"/><sz val="10"/><name val="Aptos"/><family val="2"/></font></fonts><fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF059669"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFE2E8F0"/></left><right style="thin"><color rgb="FFE2E8F0"/></right><top style="thin"><color rgb="FFE2E8F0"/></top><bottom style="thin"><color rgb="FFE2E8F0"/></bottom><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="14"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment vertical="center"/></xf><xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1"/><xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top"/></xf><xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="top"/></xf><xf numFmtId="166" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/><xf numFmtId="166" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1"/><xf numFmtId="167" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/><xf numFmtId="167" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1"/><xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/><xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1"/><xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"/><xf numFmtId="165" fontId="0" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFill="1" applyBorder="1"/></cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>
XML;
    }

    /**
     * @param  list<string>  $judulKolom
     * @param  list<array<int, bool|DateTimeInterface|float|int|string|null>>  $baris
     * @param  list<'date'|'datetime'|'decimal'|'integer'|'text'>  $tipeKolom
     */
    private function worksheetXml(string $judul, string $periode, string $dibuatPada, array $judulKolom, array $baris, array $tipeKolom): string
    {
        $kolomTerakhir = $this->hurufKolom(count($judulKolom));
        $barisTerakhir = max(4, count($baris) + 4);
        $lebarKolom = $this->hitungLebarKolom($judulKolom, $baris, $tipeKolom);
        $xmlKolom = collect($lebarKolom)->map(fn (float $lebar, int $index): string => '<col min="'.($index + 1).'" max="'.($index + 1).'" width="'.$lebar.'" customWidth="1"/>')->implode('');

        $xmlBaris = $this->barisXml(1, [$judul], ['text'], 1, 28)
            .$this->barisXml(2, ["Periode: {$periode}"], ['text'], 2, 20)
            .$this->barisXml(3, ["Dibuat pada: {$dibuatPada}"], ['text'], 2, 20)
            .$this->barisXml(4, $judulKolom, array_fill(0, count($judulKolom), 'text'), 3, 30);

        foreach ($baris as $index => $item) {
            $xmlBaris .= $this->barisXml($index + 5, $item, $tipeKolom, null, 20, $index % 2 === 1);
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            .'<dimension ref="A1:'.$kolomTerakhir.$barisTerakhir.'"/><sheetViews><sheetView workbookViewId="0" showGridLines="0"><pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            .'<sheetFormatPr defaultRowHeight="15"/><cols>'.$xmlKolom.'</cols><sheetData>'.$xmlBaris.'</sheetData>'
            .'<autoFilter ref="A4:'.$kolomTerakhir.$barisTerakhir.'"/>'
            .'<mergeCells count="3"><mergeCell ref="A1:'.$kolomTerakhir.'1"/><mergeCell ref="A2:'.$kolomTerakhir.'2"/><mergeCell ref="A3:'.$kolomTerakhir.'3"/></mergeCells>'
            .'<printOptions horizontalCentered="1"/><pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/><pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';
    }

    /**
     * @param  array<int, bool|DateTimeInterface|float|int|string|null>  $nilai
     * @param  array<int, 'date'|'datetime'|'decimal'|'integer'|'text'>  $tipeKolom
     */
    private function barisXml(int $nomorBaris, array $nilai, array $tipeKolom, ?int $gayaTetap, int $tinggi, bool $selang = false): string
    {
        $sel = '';

        foreach ($nilai as $index => $item) {
            $tipe = $tipeKolom[$index] ?? 'text';
            $gaya = $gayaTetap ?? $this->gayaData($tipe, $selang);
            $sel .= $this->selXml($this->hurufKolom($index + 1).$nomorBaris, $item, $tipe, $gaya);
        }

        return '<row r="'.$nomorBaris.'" ht="'.$tinggi.'" customHeight="1">'.$sel.'</row>';
    }

    private function selXml(string $referensi, bool|DateTimeInterface|float|int|string|null $nilai, string $tipe, int $gaya): string
    {
        if ($nilai === null || $nilai === '') {
            return '<c r="'.$referensi.'" s="'.$gaya.'"/>';
        }

        if ($nilai instanceof DateTimeInterface) {
            return '<c r="'.$referensi.'" s="'.$gaya.'" t="n"><v>'.$this->nomorTanggalExcel($nilai).'</v></c>';
        }

        if (in_array($tipe, ['integer', 'decimal'], true) && is_numeric($nilai)) {
            return '<c r="'.$referensi.'" s="'.$gaya.'" t="n"><v>'.$nilai.'</v></c>';
        }

        $teks = is_bool($nilai) ? ($nilai ? 'Ya' : 'Tidak') : (string) $nilai;

        return '<c r="'.$referensi.'" s="'.$gaya.'" t="inlineStr"><is><t xml:space="preserve">'.$this->escape($teks).'</t></is></c>';
    }

    private function gayaData(string $tipe, bool $selang): int
    {
        return match ($tipe) {
            'integer' => $selang ? 7 : 6,
            'decimal' => $selang ? 9 : 8,
            'date' => $selang ? 11 : 10,
            'datetime' => $selang ? 13 : 12,
            default => $selang ? 5 : 4,
        };
    }

    /**
     * @param  list<string>  $judulKolom
     * @param  list<array<int, bool|DateTimeInterface|float|int|string|null>>  $baris
     * @param  list<'date'|'datetime'|'decimal'|'integer'|'text'>  $tipeKolom
     * @return list<float>
     */
    private function hitungLebarKolom(array $judulKolom, array $baris, array $tipeKolom): array
    {
        $lebar = array_map(fn (string $judul): int => mb_strwidth($judul) + 3, $judulKolom);

        foreach ($baris as $item) {
            foreach ($item as $index => $nilai) {
                $tampilan = $this->nilaiTampilan($nilai, $tipeKolom[$index] ?? 'text');
                $lebar[$index] = max($lebar[$index] ?? 0, mb_strwidth($tampilan) + 2);
            }
        }

        return array_map(fn (int $nilai): float => (float) min(42, max(12, $nilai)), $lebar);
    }

    private function nilaiTampilan(bool|DateTimeInterface|float|int|string|null $nilai, string $tipe): string
    {
        if ($nilai instanceof DateTimeInterface) {
            return $nilai->format($tipe === 'datetime' ? 'd/m/Y H:i' : 'd/m/Y');
        }

        if (is_bool($nilai)) {
            return $nilai ? 'Ya' : 'Tidak';
        }

        return (string) ($nilai ?? '');
    }

    private function nomorTanggalExcel(DateTimeInterface $tanggal): float
    {
        $waktu = gmmktime(
            (int) $tanggal->format('H'),
            (int) $tanggal->format('i'),
            (int) $tanggal->format('s'),
            (int) $tanggal->format('m'),
            (int) $tanggal->format('d'),
            (int) $tanggal->format('Y'),
        );

        return round(($waktu / 86400) + 25569, 10);
    }

    private function hurufKolom(int $nomor): string
    {
        $huruf = '';

        while ($nomor > 0) {
            $nomor--;
            $huruf = chr(65 + ($nomor % 26)).$huruf;
            $nomor = intdiv($nomor, 26);
        }

        return $huruf;
    }

    private function escape(string $nilai): string
    {
        return htmlspecialchars($nilai, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
