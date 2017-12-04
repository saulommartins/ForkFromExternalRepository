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
Arquivo de instância para manutenção de estados
* Data de Criação: 22/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CSE_MAPEAMENTO."TUf.class.php"                                                 );

$stPrograma = "ManterUF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$sessao->setTrataExcecao( true );
$rsRecordSet = new RecordSet();
$obTUf       = new TUf();
$sessao->obTransacao->setMapeamento( $obTUf );

switch ($stAcao) {
    case 'incluir':
        $obTUf->setDado("cod_pais" ,$_REQUEST['inCodPais'] );
        $obTUf->setDado("nom_uf"   ,$_REQUEST['stNomeUf']  );
        $obTUf->setDado("sigla_uf" ,$_REQUEST['stSiglaUf'] );

        $stFiltro = " WHERE cod_uf        = ".$_REQUEST['inCodUf']."          \n";
        $stFiltro.= "   AND nom_municipio ='".$_REQUEST['stNomeMunicipio']."' \n";

        $obTMunicipio->recuperaTodos($rsRecordSet,$stFiltro);
        if ($rsRecordSet->getNumLinhas()<=0) {
            $obTUf->inclusao();
            SistemaLegado::alertaAviso($pgForm."?".$sessao->id."&stAcao=incluir","UF - ".$_REQUEST['stNomeUf']."","incluir","incluir_n", $sessao->id, "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($_REQUEST['stNomeUf'].' já cadastrado.'),"n_incluir","erro");
        }
   break;
    case 'alterar':
        $obTUf->setDado("cod_pais" ,$_REQUEST['inCodPais']);
        $obTUf->setDado("cod_uf"   ,$_REQUEST['inCodUf']  );
        $obTUf->setDado("nom_uf"   ,$_REQUEST['stNomeUf'] );
        $obTUf->setDado("sigla_uf" ,$_REQUEST['stSiglaUf']);
        $obTUf->alteracao();

        SistemaLegado::alertaAviso($pgList."?".$sessao->id."&stAcao=alterar","País - ".$_REQUEST['stNomeUf']."","alterar","alterar_n", $sessao->id, "../");
    break;
    case 'excluir':
        $obTUf->setDado("cod_uf",$_REQUEST['inCodUf']);
        $obTUf->exclusao();

        SistemaLegado::alertaAviso($pgList."?".$sessao->id."&stAcao=excluir","Uf - ".$_REQUEST['stNome']."","excluir","excluir_n", $sessao->id, "../");
    break;
}
$sessao->encerraExcecao();
?>
