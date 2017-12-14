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
Arquivo de instância para manutenção de países
* Data de Criação: 15/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CSE_MAPEAMENTO."TPais.class.php"                                               );

$stPrograma = "ManterPais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$sessao->setTrataExcecao( true );
$rsRecordSet = new RecordSet();
$obTPais     = new TPais();
$sessao->obTransacao->setMapeamento( $obTPais );
switch ($stAcao) {
    case 'incluir':

        $obTPais->setDado("cod_rais"     ,$_REQUEST['inCodRais']      );
        $obTPais->setDado("nom_pais"     ,$_REQUEST['stNome']         );
        $obTPais->setDado("nacionalidade",$_REQUEST['stNacionalidade']);

        $stFiltro = " WHERE cod_rais = ".$_REQUEST['inCodUf']." \n";
        $stFiltro.= "   AND nom_pais ='".$_REQUEST['stNome']."' \n";

        $obTPais->recuperaTodos($rsRecordSet,$stFiltro);
        if ($rsRecordSet->getNumLinhas()<=0) {
            $obTPais->inclusao();
            SistemaLegado::alertaAviso($pgForm."?".$sessao->id."&stAcao=incluir","País - ".$_REQUEST['stNome']."","incluir","incluir_n", $sessao->id, "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($_REQUEST['stNome'].' já cadastrado.'),"n_incluir","erro");
        }
    break;
    case 'alterar':
        $obTPais->setDado("cod_pais"     ,$_REQUEST['inCodPais']      );
        $obTPais->setDado("cod_rais"     ,$_REQUEST['inCodRais']      );
        $obTPais->setDado("nom_pais"     ,$_REQUEST['stNome']         );
        $obTPais->setDado("nacionalidade",$_REQUEST['stNacionalidade']);
        $obTPais->alteracao();

        SistemaLegado::alertaAviso($pgForm."?".$sessao->id."&stAcao=alterar","País - ".$_REQUEST['stNome']."","alterar","alterar_n", $sessao->id, "../");
    break;
    case 'excluir':
        $obTPais->setDado("cod_pais"     ,$_REQUEST['inCodPais']      );
        $obTPais->exclusao();
        SistemaLegado::alertaAviso($pgList."?".$sessao->id."&stAcao=excluir","País - ".$_REQUEST['stNome']."","excluir","excluir_n", $sessao->id, "../");
    break;
}
$sessao->encerraExcecao();
?>
