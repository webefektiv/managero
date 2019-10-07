<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package managero
 */

?>

<section id="searchWrap" style="background-image: url('<?php the_field('background_image',16); ?>');">
    <div class="row">
        <div class="col-12 col-md-8 offset-md-2">
            <div class="page-content">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true"><?php esc_html_e( 'joburi', 'managero' ); ?></a>
                        <a class="nav-item nav-link" id="nav-agajati-tab" data-toggle="tab" href="#nav-agajati" role="tab" aria-controls="nav-agajati" aria-selected="false"><?php esc_html_e( 'angajati', 'managero' ); ?></a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <form >

                            <input type="text" value="" placeholder="job" />
                            <div class="ui-widget">
                                <select id="combobox">
                                    <option value="">Alege orasul</option>
                                    <option value="0">Alege Judet</option>
                                    <option value="Alba">Alba</option>
                                    <option value="Arad">Arad</option>
                                    <option value="Arges">Arges</option>
                                    <option value="Bacau">Bacau</option>
                                    <option value="Bihor">Bihor</option>
                                    <option value="Bistrita Nasaud">Bistrita Nasaud</option>
                                    <option value="Botosani">Botosani</option>
                                    <option value="Brasov">Brasov</option>
                                    <option value="Braila">Braila</option>
                                    <option value="Bucuresti">Bucuresti</option>
                                    <option value="Buzau">Buzau</option>
                                    <option value="Caras Severin">Caras Severin</option>
                                    <option value="Calarasi">Calarasi</option>
                                    <option value="Cluj">Cluj</option>
                                    <option value="Constanta">Constanta</option>
                                    <option value="Covasna">Covasna</option>
                                    <option value="Dambovita">Dambovita</option>
                                    <option value="Dolj">Dolj</option>
                                    <option value="Galati">Galati</option>
                                    <option value="Giurgiu">Giurgiu</option>
                                    <option value="Gorj">Gorj</option>
                                    <option value="Harghita">Harghita</option>
                                    <option value="Hunedoara">Hunedoara</option>
                                    <option value="Ialomita">Ialomita</option>
                                    <option value="Iasi">Iasi</option>
                                    <option value="Ilfov">Ilfov</option>
                                    <option value="Maramures">Maramures</option>
                                    <option value="Mehedinti">Mehedinti</option>
                                    <option value="Mures">Mures</option>
                                    <option value="Neamt">Neamt</option>
                                    <option value="Olt">Olt</option>
                                    <option value="Prahova">Prahova</option>
                                    <option value="Satu Mare">Satu Mare</option>
                                    <option value="Salaj">Salaj</option>
                                    <option value="Sibiu">Sibiu</option>
                                    <option value="Suceava">Suceava</option>
                                    <option value="Teleorman">Teleorman</option>
                                    <option value="Timis">Timis</option>
                                    <option value="Tulcea">Tulcea</option>
                                    <option value="Vaslui">Vaslui</option>
                                    <option value="Valcea">Valcea</option>
                                    <option value="Vrancea">Vrancea</option>
                                </select>
                            </div>

                            <input type="submit" value="<?php esc_html_e( 'cauta', 'managero' ); ?>" />

                        </form>
                    </div>
                    <div class="tab-pane fade" id="nav-agajati" role="tabpanel" aria-labelledby="nav-agajati-tab">
                        <form>

                            <input type="text" value="" placeholder="job" />
                            <div class="ui-widget">
                                <select id="combobox2">
                                    <option value="">Alege orasul</option>
                                    <option value="0">Alege Judet</option>
                                    <option value="Alba">Alba</option>
                                    <option value="Arad">Arad</option>
                                    <option value="Arges">Arges</option>
                                    <option value="Bacau">Bacau</option>
                                    <option value="Bihor">Bihor</option>
                                    <option value="Bistrita Nasaud">Bistrita Nasaud</option>
                                    <option value="Botosani">Botosani</option>
                                    <option value="Brasov">Brasov</option>
                                    <option value="Braila">Braila</option>
                                    <option value="Bucuresti">Bucuresti</option>
                                    <option value="Buzau">Buzau</option>
                                    <option value="Caras Severin">Caras Severin</option>
                                    <option value="Calarasi">Calarasi</option>
                                    <option value="Cluj">Cluj</option>
                                    <option value="Constanta">Constanta</option>
                                    <option value="Covasna">Covasna</option>
                                    <option value="Dambovita">Dambovita</option>
                                    <option value="Dolj">Dolj</option>
                                    <option value="Galati">Galati</option>
                                    <option value="Giurgiu">Giurgiu</option>
                                    <option value="Gorj">Gorj</option>
                                    <option value="Harghita">Harghita</option>
                                    <option value="Hunedoara">Hunedoara</option>
                                    <option value="Ialomita">Ialomita</option>
                                    <option value="Iasi">Iasi</option>
                                    <option value="Ilfov">Ilfov</option>
                                    <option value="Maramures">Maramures</option>
                                    <option value="Mehedinti">Mehedinti</option>
                                    <option value="Mures">Mures</option>
                                    <option value="Neamt">Neamt</option>
                                    <option value="Olt">Olt</option>
                                    <option value="Prahova">Prahova</option>
                                    <option value="Satu Mare">Satu Mare</option>
                                    <option value="Salaj">Salaj</option>
                                    <option value="Sibiu">Sibiu</option>
                                    <option value="Suceava">Suceava</option>
                                    <option value="Teleorman">Teleorman</option>
                                    <option value="Timis">Timis</option>
                                    <option value="Tulcea">Tulcea</option>
                                    <option value="Vaslui">Vaslui</option>
                                    <option value="Valcea">Valcea</option>
                                    <option value="Vrancea">Vrancea</option>
                                </select>
                            </div>

                            <input type="submit" value="<?php esc_html_e( 'cauta', 'managero' ); ?>" />

                        </form>
                    </div>
                </div>
            </div><!-- .page-content -->
            <footer class="entry-footer">

            </footer>
        </div>
    </div>


</section><!-- .no-results -->
