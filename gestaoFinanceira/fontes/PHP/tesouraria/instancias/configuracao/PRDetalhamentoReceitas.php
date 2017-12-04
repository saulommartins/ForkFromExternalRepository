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
    * Página de Processamento de Receitas
    * Data de Criação   : 12/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-03-11 16:01:21 -0300 (Ter, 11 Mar 2008) $

    * Casos de uso: uc-02.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "DetalhamentoReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//inicializacao
$arCreditos = array();
$arAcrescimos = array();

// dividir array entre creditos e acrescimos
foreach ( Sessao::read('arCredito') as $arLinha ) {
    if ($arLinha['cod_tipo'] != '0') {
        $arAcrescimos[]  = $arLinha;
    } else {
        $arCreditos[] = $arLinha;
    }
}

// instanciar classes de mapeamento de acordo com tipo de receita
switch ($_REQUEST['stTipoReceita']) {
    case 'orcamentaria':
        require_once( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoReceitaCredito.class.php');
        require_once( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoReceitaCreditoDesconto.class.php');
        require_once( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoReceitaCreditoAcrescimo.class.php');
        $obTRecCredito = new TOrcamentoReceitaCredito();
        $obTRecCreditoDesconto = new TOrcamentoReceitaCreditoDesconto();
        $obTRecCreditoAcrescimo = new TOrcamentoReceitaCreditoAcrescimo();

        $stCampoCodigo = 'cod_receita';

    break;

    case 'extra':
        require_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnaliticaCredito.class.php" );
        require_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnaliticaCreditoAcrescimos.class.php" );
        $obTRecCredito = new TContabilidadePlanoAnaliticaCredito;
        $obTRecCreditoAcrescimo = new TContabilidadePlanoAnaliticaCreditoAcrescimos;

        $stCampoCodigo = 'cod_plano';

    break;
}

// para validação
$obTRecCredito->setDado( 'codigo' , $_REQUEST['inCodigo']  );
$obTRecCreditoAcrescimo->setDado( 'codigo' , $_REQUEST['inCodigo']  );

$boFlagTransacao = false;
$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

if ( !$obErro->ocorreu() ) {
    /**
    * limpar creditos/acrescimos antigos
    **/
    // desconto
    if ($_POST['stTipoReceita'] != 'extra') {
        $pk_tmp = $obTRecCreditoDesconto->getComplementoChave();
        $obTRecCreditoDesconto->setComplementoChave('exercicio, '.$stCampoCodigo);
        $obTRecCreditoDesconto->setDado( $stCampoCodigo , $_REQUEST['inCodigo']  ); // cod_receita(orcamentaria) ou cod_plano(extra)
        $obTRecCreditoDesconto->setDado( 'exercicio', $_REQUEST['stExercicio'] );
        $obErro = $obTRecCreditoDesconto->exclusao($boTransacao);
    }

    // credito
    $pk_tmp = $obTRecCredito->getComplementoChave();
    $obTRecCredito->setComplementoChave('exercicio, '.$stCampoCodigo);

    $obTRecCredito->setDado( $stCampoCodigo , $_REQUEST['inCodigo']  ); // cod_receita(orcamentaria) ou cod_plano(extra)
    $obTRecCredito->setDado( 'exercicio', $_REQUEST['stExercicio'] );
    $obErro = $obTRecCredito->exclusao($boTransacao);

    $obTRecCredito->setComplementoChave($pk_tmp);

  // acrescimo
    if ( !$obErro->ocorreu() ) {
        $pk_tmp = $obTRecCreditoAcrescimo->getComplementoChave();
        $obTRecCreditoAcrescimo->setComplementoChave('exercicio, '.$stCampoCodigo);

        $obTRecCreditoAcrescimo->setDado( $stCampoCodigo , $_REQUEST['inCodigo']  );
        $obTRecCreditoAcrescimo->setDado( 'exercicio', $_REQUEST['stExercicio'] );
        $obErro = $obTRecCreditoAcrescimo->exclusao($boTransacao);

        $obTRecCreditoAcrescimo->setComplementoChave($pk_tmp);
        if ( !$obErro->ocorreu() ) {
            /**
            * Inserir creditos e acrescimos
            **/
            // inserir creditos
            if ( count( $arCreditos ) > 0 || count( $arAcrescimos ) > 0 ) {
                foreach ($arCreditos as $Credito) {
                    $arCodCredito = explode( '.' , $Credito['codigo'] );
                    $obTRecCredito->setDado('cod_credito' , trim( $arCodCredito[0] * 1) );
                    $obTRecCredito->setDado('cod_especie' , trim( $arCodCredito[1] * 1) );
                    $obTRecCredito->setDado('cod_genero'  , trim( $arCodCredito[2] * 1) );
                    $obTRecCredito->setDado('cod_natureza', trim( $arCodCredito[3] * 1) );
                    $obTRecCredito->setDado('divida_ativa', $Credito['divida_ativa'] == '1' ? 'true' : 'false' );

                    if ($Credito['cod_dedutora'] != '') {
                        $obTRecCreditoDesconto->setDado($stCampoCodigo, $_REQUEST['inCodigo'] );
                        $obTRecCreditoDesconto->setDado('exercicio'           , $_REQUEST['stExercicio'] );
                        $obTRecCreditoDesconto->setDado('cod_credito'         , trim( $arCodCredito[0] * 1) );
                        $obTRecCreditoDesconto->setDado('cod_especie'         , trim( $arCodCredito[1] * 1) );
                        $obTRecCreditoDesconto->setDado('cod_genero'          , trim( $arCodCredito[2] * 1) );
                        $obTRecCreditoDesconto->setDado('cod_natureza'        , trim( $arCodCredito[3] * 1) );
                        $obTRecCreditoDesconto->setDado('exercicio_dedutora'  , $_REQUEST['stExercicio'] );
                        $obTRecCreditoDesconto->setDado('cod_receita_dedutora', $Credito['cod_dedutora'] );
                        $obTRecCreditoDesconto->setDado('divida_ativa'        , $Credito['divida_ativa'] == '1' ? 'true' : 'false' );
                    }

                    // validar antes de incluir
                    if (get_class($obTRecCredito) == 'TOrcamentoReceitaCredito') {
                        $obTRecCredito->recuperaClassReceitasCreditosValidacaoOrcamento( $rsValidaCredito ,'','',$boTransacao);
                    } else {
                        $obTRecCredito->recuperaClassReceitasCreditosValidacao( $rsValidaCredito ,'','',$boTransacao);
                    }
                    if ( $rsValidaCredito->getNumLinhas() > 0 ) {

                        $obErro->setDescricao('Crédito já vinculado!');
                        break;
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obErro = $obTRecCredito->inclusao( $boTransacao );
                        if ($Credito['cod_dedutora'] != '') {
                            $obErro = $obTRecCreditoDesconto->inclusao( $boTransacao );
                        }
                    }
                }

                // inserir acrescimos
                if ( !$obErro->ocorreu() and count( $arAcrescimos ) > 0 ) {
                    foreach ($arAcrescimos as $CreditoAcrescimos) {
                        $arCodCredito = explode( '.' , $CreditoAcrescimos['codigo'] );
                        $obTRecCreditoAcrescimo->setDado('cod_credito'  , trim( $arCodCredito[0] * 1 )  );
                        $obTRecCreditoAcrescimo->setDado('cod_especie'  , trim( $arCodCredito[1] * 1 )  );
                        $obTRecCreditoAcrescimo->setDado('cod_genero'   , trim( $arCodCredito[2] * 1 )  );
                        $obTRecCreditoAcrescimo->setDado('cod_natureza' , trim( $arCodCredito[3] * 1 )  );
                        $obTRecCreditoAcrescimo->setDado('cod_acrescimo', trim( $CreditoAcrescimos['cod_acrescimo'] )  );
                        $obTRecCreditoAcrescimo->setDado('cod_tipo'     , trim( $CreditoAcrescimos['cod_tipo'] )  );
                        $obTRecCreditoAcrescimo->setDado('divida_ativa' , $CreditoAcrescimos['divida_ativa'] == '1' ? 'true' : 'false' );

                        // validar antes de incluir
                        $obTRecCreditoAcrescimo->recuperaClassReceitasCreditosValidacao( $rsValidaCredito ,'','',$boTransacao);
                        if ( $rsValidaCredito->getNumLinhas() > 0 ) {
                            $obErro->setDescricao('Crédito/Acréscimo já vinculado!');
                            break;
                        }

                        if ( !$obErro->ocorreu() ) {
                            $obErro = $obTRecCreditoAcrescimo->inclusao( $boTransacao );
                        }
                    }
                }
            }
        }
    }
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,  $obTContabilidadePlanoAnaliticaCredito );

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso('FMManterReceitas.php',$_REQUEST['inCodigo'].'/'.$_REQUEST['stExercicio'],"incluir","aviso", Sessao::getId(), "../");
} else {
    if ( substr_count( $obErro->getDescricao() , 'duplicar chave') > 0 ) {
        $obErro->setDescricao( 'Crédito já vinculado' );
    }
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}
