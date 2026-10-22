const _bulan = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
const _bulanPendek = [
  'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
  'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
];
const _hariPendek = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

String monthYear(DateTime d) => '${_bulan[d.month - 1]} ${d.year}';

String shortDate(DateTime d) => '${d.day} ${_bulanPendek[d.month - 1]} ${d.year}';

String dayShort(DateTime d) => _hariPendek[d.weekday - 1];

String dateTime(DateTime d) =>
    '${shortDate(d)} ${d.hour.toString().padLeft(2, '0')}:${d.minute.toString().padLeft(2, '0')}';
