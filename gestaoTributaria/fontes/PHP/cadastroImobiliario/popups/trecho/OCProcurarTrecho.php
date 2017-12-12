<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de processamento oculto para o cadastro de trecho
    * Data de Criação   : 21/10/2004

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCProcurarTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.6  2007/01/19 15:57:41  cercato
Bug #8148#

Revision 1.5  2006/09/15 15:04:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma				= "ProcurarTrecho";
$pgList					= "LS".$stPrograma.".php";
$pgForm					= "FM".$stPrograma.".php";
$pgOCult				= "OC".$stPrograma.".php";
$pgJs					= "JS".$stPrograma.".js";

include_once ( $pgJs );

//Instancia Objeto
$obTMunicipio = new TMunicipio;
$rsMunicipios = new RecordSet;

// SELECIONA ACAO
switch ($_REQUEST ["stCtrl"]) {
    case "preencheMunicipio":
        $stFiltro = " WHERE cod_uf = ".$_REQUEST["cmbUF"];
        $stJs .= "f.inCodigoMunicipio.value=''; \n";
        $stJs .= "limpaSelect(f.cmbMunicipio,0); \n";
        $stJs .= "f.cmbMunicipio[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["cmbUF"]) {
            $obTMunicipio->recuperaTodos( $rsMunicipios, $stFiltro, "", $boTransacao );
            $inContador = 1;
        }
        while ( !$rsMunicipios->eof() ) {
            $inCodigoMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
            $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
            $stJs .= "f.cmbMunicipio.options[$inContador] = new Option('".$stNomMunicipio."','".$inCodigoMunicipio."'); \n";
            $inContador++;
            $rsMunicipios->proximo();
        }
        if ($_REQUEST["stLimpar"] == "limpar") {
            $stJs .= "f.inCodigoMunicipio.value='".$_REQUEST["inCodigoMunicipio"]."'; \n";
            $stJs .= "f.cmbMunicipio.options[".$_REQUEST["inCodigoMunicipio"]."].selected = true; \n";
        }
    break;
}
if ($stJs) {
    sistemaLegado::executaIFrameOculto($stJs);
}
?>
