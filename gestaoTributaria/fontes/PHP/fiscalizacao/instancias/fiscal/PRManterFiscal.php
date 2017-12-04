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
    * Página oculta do formulário de Fiscal

    * Data de Criação   : 20/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: PRManterFiscal.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISFiscal.class.php"                                               );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISFiscalFiscalizacao.class.php"                                   );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISTipoFiscalizacao.class.php"                                     );
include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFiscal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

switch ($_REQUEST['stAcao']) {
    case 'excluir':
    # Inicia nova transação
    $obTransacao = new Transacao();
        $boFlagTransacao = false;
           $boTransacao = "";
    $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $obTFiscal             = new TFISFiscal();
        $obTFiscalFiscalizacao = new TFISFiscalFiscalizacao();

        $obTFiscalFiscalizacao->setDado( "cod_fiscal", $_REQUEST["cod_fiscal"] );
        $obErro = $obTFiscalFiscalizacao->exclusao($boTransacao);

        $obTFiscal->setDado( "cod_fiscal",$_REQUEST['cod_fiscal'] );
        $obErro = $obTFiscal->exclusao($boTransacao);
        $stCaminho = $pgList."?".Sessao::getId()."&stAcao=excluir";

    if ($obErro->ocorreu()) {
        sistemaLegado::alertaAviso($stCaminho,"Fiscal já vinculado com algum Processos Fiscal!","n_excluir","erro",Sessao::getId(), "../");
    }
     # Termina transação
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFiscal );
        sistemaLegado::alertaAviso($stCaminho,$_REQUEST['cod_fiscal'] ,"excluir","aviso",Sessao::getId(),"../");
        break;
    case 'alterar':
        $obTFiscal             = new TFISFiscal();
        $obTFiscalFiscalizacao = new TFISFiscalFiscalizacao();
        $rsRecordSetFiscal     = new RecordSet();

        $inNumCgm = explode( "-",$_REQUEST['hdnCGM']);
        $inNumCgm = intval( $inNumCgm[0] );

        $obTFiscal->setDado( "cod_fiscal"  , $_REQUEST['inFiscal']                             );
        $obTFiscal->setDado( "administrador",( $_REQUEST['boFuncao']=="Administrador" ) ? "true" : "false"        	       );
        $obTFiscal->setDado( "ativo"       ,( $_REQUEST['boAtivo']=="Sim" ) ? "true" : "false" );
        $obTFiscal->setDado( "numcgm"      , $inNumCgm                                         );
        $obTFiscal->setDado( "cod_contrato", $_REQUEST['inContrato']                           );
        $obTFiscal->alteracao();

        $obTFiscalFiscalizacao->setDado("cod_fiscal",$_REQUEST['inFiscal']);
        $obTFiscalFiscalizacao->exclusao();
        $arValores = Sessao::read( 'arValores' );
        if (count($arValores)>0) {
            foreach ($arValores as $key) {
                $obTFiscalFiscalizacao->setDado( "cod_fiscal", $_REQUEST["inFiscal"] );
                $obTFiscalFiscalizacao->setDado( "cod_tipo"  , $key['cod_tipo']      );
                $obTFiscalFiscalizacao->inclusao();
            }
            SistemaLegado::alertaAviso($pgList , $_REQUEST['inFiscal'] ,"alterar","aviso", Sessao::getId(),"../");
        } else {
            SistemaLegado::exibeAviso( "Deve existir ao menos um dado para atribuição","n_incluir","aviso" );
        }
        break;
    case 'incluir':
        $obTFiscalFiscalizacao = new TFISFiscalFiscalizacao();
        $obTFiscal             = new TFISFiscal();
        $obTPessoalContrato    = new TPessoalContrato();

        $arValores = Sessao::read( 'arValores' );
        if (count($arValores)>0) {

            $rsRecordSetContrato = new RecordSet();
            $rsRecordSetFiscal   = new RecordSet();

            $stFiltro = " WHERE registro = ".$_REQUEST['inContrato'];

            $obTPessoalContrato->recuperaTodos($rsRecordSetContrato, $stFiltro);

            $inNumCgm = explode( "-",$_REQUEST['hdnCGM']);
            $inNumCgm = intval( $inNumCgm[0] );

            $stFiltro = " WHERE numcgm       = ".$inNumCgm."                                      \n";
            $stFiltro.= "   AND cod_contrato = ".$rsRecordSetContrato->getCampo("cod_contrato")." \n";

            $obTFiscal->recuperaTodos( $rsRecordSetFiscal,$stFiltro );
            $obTFiscal->proximoCod( $inNumFiscal );

            if ($rsRecordSetFiscal->Eof()) {
                $obTFiscal->setDado( "cod_fiscal"  , $inNumFiscal                                       );
                $obTFiscal->setDado( "numcgm"      ,$inNumCgm                                          );

                $obTFiscal->setDado( "cod_contrato", $rsRecordSetContrato->getCampo("cod_contrato")     );
                $obTFiscal->setDado( "administrador"      ,      ( $_REQUEST['boFuncao']=="Administrador" ) ? "true" : "false"                                      );
                $obTFiscal->setDado( "ativo"       , ( $_REQUEST['boAtivo']=="Sim" ) ? "true" : "false" );
                $obTFiscal->inclusao();
                foreach ($arValores as $key) {
                    $obTFiscalFiscalizacao->setDado( "cod_fiscal", $obTFiscal->getDado("cod_fiscal")  );
                    $obTFiscalFiscalizacao->setDado( "cod_tipo"  , $key['cod_tipo']                   );
                    $obTFiscalFiscalizacao->inclusao();
                }
                    sistemaLegado::alertaAviso($pgForm , $inNumFiscal ,"incluir","aviso", Sessao::getId(), "../");
            } else {
                $matricula = $_REQUEST['inContrato'];
                sistemaLegado::exibeAviso( "Fiscal já cadastrado para essa matrícula.(".$matricula.")","n_incluir","erro" );
            }
        } else {
            sistemaLegado::exibeAviso( "Deve existir ao menos um dado para atribuição","n_incluir","aviso" );
        }
        break;
}
