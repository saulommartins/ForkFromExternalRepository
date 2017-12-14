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
/*
Arquivo de instância para manutenção de municipios
* Data de Criação: 25/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CSE_MAPEAMENTO."TMunicipio.class.php"                                          );

$stPrograma = "ManterMunicipio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$sessao->setTrataExcecao( true );
$rsRecordSet  = new RecordSet();
$obTMunicipio = new TMunicipio();
$sessao->obTransacao->setMapeamento( $obTMunicipio );

switch ($stAcao) {
    case 'incluir':

        $obTMunicipio->setDado("cod_pais"      ,$_REQUEST['inCodPais']      );
        $obTMunicipio->setDado("cod_uf"        ,$_REQUEST['inCodUf']        );
        $obTMunicipio->setDado("nom_municipio" ,$_REQUEST['stNomeMunicipio']);

        $stFiltro = " WHERE cod_uf        = ".$_REQUEST['inCodUf']."          \n";
        $stFiltro.= "   AND nom_municipio ='".$_REQUEST['stNomeMunicipio']."' \n";

        $obTMunicipio->recuperaTodos($rsRecordSet,$stFiltro);
        if ($rsRecordSet->getNumLinhas()<=0) {
            $obTMunicipio->inclusao();
            SistemaLegado::alertaAviso($pgForm."?".$sessao->id."&stAcao=incluir","Município - ".$_REQUEST['stNomeMunicipio']."","incluir","incluir_n", $sessao->id, "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($_REQUEST['stNomeMunicipio'].' já cadastrado.'),"n_incluir","erro");
        }

    break;
    case 'alterar':

        $obTMunicipio->setDado("cod_pais"     ,$_REQUEST['inCodPais']      );
        $obTMunicipio->setDado("cod_uf"       ,$_REQUEST['inCodUf']        );
        $obTMunicipio->setDado("cod_municipio",$_REQUEST['inCodMunicipio'] );
        $obTMunicipio->setDado("nom_municipio",$_REQUEST['stNomeMunicipio']);
        $obTMunicipio->alteracao();

        SistemaLegado::alertaAviso($pgList."?".$sessao->id."&stAcao=alterar","Município - ".$_REQUEST['stNomeMunicipio']."","alterar","alterar_n", $sessao->id, "../");
    break;
    case 'excluir':

        $obTMunicipio->setDado("cod_municipio",$_REQUEST['inCodMunicipio']);
        $obTMunicipio->setDado("cod_uf"       ,$_REQUEST['inCodUf']       );
        $obTMunicipio->exclusao();

        SistemaLegado::alertaAviso($pgList."?".$sessao->id."&stAcao=excluir","Município - ".$_REQUEST['stNomeMunicipio']."","excluir","excluir_n", $sessao->id, "../");
    break;
}
$sessao->encerraExcecao();
?>
