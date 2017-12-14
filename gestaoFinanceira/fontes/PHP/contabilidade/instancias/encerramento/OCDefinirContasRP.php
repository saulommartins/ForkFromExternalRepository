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
    * Oculto para Definir contas para inscrição de RP
    * Data de Criação   :28/12/2006

    * @author Analista: Cleisson
    * @author Desenvolvedor:Bruce Cruz de Sena

    * @ignore

    * $Id: OCDefinirContasRP.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "DefinirContasRP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stJs = '';

function addContaCredito($inCodEntidade,  $inCodConta, $stDescricaoConta = '',$inCodTipo)
{

    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeTipoContaLancamentoRp.class.php';

    $arConta = array ();
    $stMensagem = '';

    if (!$inCodEntidade) {
        $stMensagem = "Escolha uma entidade." ;
    } else { if (!$inCodConta) {
                $stMensagem = "Escolha uma conta.";
           } else {
                $arContasCredito = Sessao::read('arContasCredito');
                if ( is_array ( $arContasCredito ) ) {
                    foreach ($arContasCredito as $registro) {
                        if ( ($registro['cod_entidade'] ==  $inCodEntidade) and ( $registro['cod_conta'] == $inCodConta) ) {
                            $stMensagem = "Esta conta já foi cadastrada para esta entidade";
                        }
                     }
                 }
           }
    }

    if (!$stMensagem) {
        $arConta['cod_entidade'   ] = $inCodEntidade;
        $arConta['cod_conta'      ] = $inCodConta;
        $arConta['descricao_conta'] = $stDescricaoConta;
        $arConta['cod_tipo']        = $inCodTipo;

        $rsTipoConta = new RecordSet;
        $obTContabilidadeTipoContaLancamentoRp   = new TContabilidadeTipoContaLancamentoRp;
        $obTContabilidadeTipoContaLancamentoRp->setDado ( 'cod_tipo_conta' , $inCodTipo );
        $obTContabilidadeTipoContaLancamentoRp->setDado ( 'exercicio', Sessao::getExercicio() );
        $obTContabilidadeTipoContaLancamentoRp->consultar();
        $arConta['descricao_tipo'] =   $obTContabilidadeTipoContaLancamentoRp->getDado ( 'descricao' ); ;

        $inCodigoCredito = Sessao::read('codigoCredito');
        $arConta['codigo'] = $inCodigoCredito++;
        Sessao::write('codigoCredito', $inCodigoCredito);

        $arContasCredito = Sessao::read('arContasCredito');
        $arContasCredito[] = $arConta;
        Sessao::write('arContasCredito', $arContasCredito);

        $stJs = montaSpanContasCredito();
    } else {
        echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;

}

function montaSpanContasCredito()
{
    $arContasCredito = Sessao::read('arContasCredito');
    $rsRecordSet = new RecordSet;
    if ( is_array ( $arContasCredito ) ) {
        $rsRecordSet->preenche ( $arContasCredito );
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Lista de contas incluídas');
    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Entidade" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Conta" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[cod_tipo] - [descricao_tipo]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "cod_conta" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao_conta" );
    $obLista->commitDado();

    // Adicionando ação excluir a lista

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delContaCredito');" );
    $obLista->ultimaAcao->addCampo('', "&inCodigo=[codigo]" );
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnContasCredito').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnContasCredito').innerHTML = '".$html."';\n";

    return $stJs;

}

function addContaDebito($inCodEntidade, $inCodTipo, $inCodConta, $stDescricaoConta = '')
{
    include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeTipoContaLancamentoRp.class.php';

    $arContasCredito = Sessao::read('arContasDebito');
    $stMensagem = '';
    if ( is_array (  $arContasCredito ) ) {

        foreach ($arContasCredito as $registro) {
            if ( ($registro['cod_entidade'] ==  $inCodEntidade) and ( $registro['cod_conta'] == $inCodConta) and ( $inCodTipo == $registro['cod_tipo']) ) {
                $stMensagem = "Esta conta já foi cadastrada para esta entidade";
            }
        }
    }

    if (!$stMensagem) {

        $arConta = array();

        $arConta['cod_entidade'   ] = $inCodEntidade;
        $arConta['cod_conta'      ] = $inCodConta;
        $arConta['descricao_conta'] = $stDescricaoConta;
        $arConta['cod_tipo']        = $inCodTipo;

        $rsTipoConta = new RecordSet;

        $obTContabilidadeTipoContaLancamentoRp   = new TContabilidadeTipoContaLancamentoRp;
        $obTContabilidadeTipoContaLancamentoRp->setDado ( 'cod_tipo_conta' , $inCodTipo );
        $obTContabilidadeTipoContaLancamentoRp->setDado ( 'exercicio', Sessao::getExercicio() );
        $obTContabilidadeTipoContaLancamentoRp->consultar();
        $arConta['descricao_tipo'] =   $obTContabilidadeTipoContaLancamentoRp->getDado ( 'descricao' ); ;

        $inCodigoDebito = Sessao::read('codigoDebito');
        $arConta['codigo'] = $inCodigoDebito++;
        Sessao::write('codigoDebito', $inCodigoDebito);

        $arContasDebito = Sessao::read('arContasDebito');
        $arContasDebito[] = $arConta;
        Sessao::write('arContasDebito', $arContasDebito);

        $stJs = montaSpanContasDebito();
    } else {
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;

}

function montaSpanContasDebito()
{
    $rsRecordSet = new RecordSet;
    $arContasDebito = Sessao::read('arContasDebito');
    if ( is_array ( $arContasDebito ) >0 ) {
        $rsRecordSet->preenche ( $arContasDebito );
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( 'Lista de contas incluídas');
    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Entidade" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Conta" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[cod_tipo] - [descricao_tipo]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "cod_conta" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao_conta" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delContaDebito');" );
    $obLista->ultimaAcao->addCampo('', "&inCodigo=[codigo]" );
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnContasDebito').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnContasDebito').innerHTML = '".$html."';\n";

    return $stJs;

}

function buscaContas()
{

    include_once ( CAM_GF_CONT_MAPEAMENTO.'TContabilidadeContaLancamentoRp.class.php'  );

    $obTContabilidadeContaLancamentoRp = new TContabilidadeContaLancamentoRp;

    $rsContasCredito = new RecordSet;
    $stFiltro = " where conta_lancamento_rp.cod_tipo_conta = 0 and conta_lancamento_rp.exercicio = '".Sessao::getExercicio()."' ";

    $obTContabilidadeContaLancamentoRp->recuperaRelacionamento ( $rsContasCredito, $stFiltro );

    $inCodigoCredito = 0;

    $arContasDebito = Sessao::read('arContasDebito');
    while ( !$rsContasCredito->eof() ) {
        $arConta['cod_entidade'   ] = $rsContasCredito->getCampo( 'cod_entidade'    );
        $arConta['cod_conta'      ] = $rsContasCredito->getCampo( 'cod_plano'       );
        $arConta['descricao_conta'] = $rsContasCredito->getCampo( 'descricao_conta' );
        $arConta['cod_tipo'       ] = $rsContasCredito->getCampo( 'cod_tipo_conta'  );
        $arConta['descricao_tipo' ] = $rsContasCredito->getCampo( 'descricao_tipo'  );
        $arConta['codigo'         ] = $inCodigoCredito++;
        $arContasCredito[] = $arConta ;
        $rsContasCredito->proximo();
    }

    Sessao::write('arContasCredito', $arContasCredito);

    $rsContasDebito = new RecordSet;
    $stFiltro = " where conta_lancamento_rp.cod_tipo_conta <> 0 and conta_lancamento_rp.exercicio = '".Sessao::getExercicio()."' ";
    $obTContabilidadeContaLancamentoRp->recuperaRelacionamento ( $rsContasDebito, $stFiltro );

    $inCodigoDebito = 0;
    $arConta = array();
    while ( !$rsContasDebito->eof() ) {
        $arConta['cod_entidade'   ] = $rsContasDebito->getCampo( 'cod_entidade'    );
        $arConta['cod_conta'      ] = $rsContasDebito->getCampo( 'cod_plano'       );
        $arConta['descricao_conta'] = $rsContasDebito->getCampo( 'descricao_conta' );
        $arConta['cod_tipo'       ] = $rsContasDebito->getCampo( 'cod_tipo_conta'  );
        $arConta['descricao_tipo' ] = $rsContasDebito->getCampo( 'descricao_tipo'  );
        $arConta['codigo'         ] = $inCodigoDebito++;
        $arContasDebito[] = $arConta ;
        $rsContasDebito->proximo();
    }
    Sessao::write('arContasDebito', $arContasDebito);

    $stJs  = montaSpanContasCredito();
    $stJs .= montaSpanContasDebito();

    Sessao::write('codigoDebito', $inCodigoDebito);
    Sessao::write('codigoCredito',$inCodigoCredito);

    sistemaLegado::executaFrameOculto( $stJs );
}

function delContaDebito($inCodigo)
{

    $arContasDebito = Sessao::read('arContasDebito');
    $arContas = array();
    foreach ($arContasDebito as $registro) {
        if ($registro['codigo'] != $inCodigo) {
            $arContas[] = $registro;
        }
    }
    Sessao::write('arContasDebito', $arContas);

    $stJs = montaSpanContasDebito();

    return $stJs;
}

function delContaCredito($inCodigo)
{

    $arContasCredito = Sessao::read('arContasCredito');
    $arContas = array();
    foreach ($arContasCredito as $registro) {
        if ($registro['codigo'] != $inCodigo) {
            $arContas[] = $registro;
        }
    }
    Sessao::write('arContasCredito', $arContas);

    $stJs = montaSpanContasCredito();

    return $stJs;
}

switch ($_REQUEST ['stCtrl']) {

case 'incluirContaCredito':
    if ($_REQUEST['inCodContaCredito_tipo_6']) {
        addContaCredito ( $_REQUEST['inCodEntidadeCredito'],    $_REQUEST['inCodContaCredito_tipo_6'] , $_REQUEST['innerContaPagarNaoProcessados'], '0'   ) ;
    }
    $stJs = "if (document.getElementById('stNomEntidadeCredito').selectedIndex > 0) {
                document.getElementById('inCodEntidadeCredito').value = '';
                document.getElementById('stNomEntidadeCredito').value = '';
                document.getElementById('inCodContaCredito_tipo_6').value = '';
                document.getElementById('innerContaPagarNaoProcessados').innerHTML = '&nbsp;';
             } else {
                document.getElementById('inCodContaCredito_tipo_6').value = '';
                document.getElementById('innerContaPagarNaoProcessados').innerHTML = '&nbsp;';
             } ";
    $stJs .= montaSpanContasCredito();
    break;

case 'incluirContaDebito':

    if (!$_REQUEST['inCodEntidade']) {
        $stMensagem = "Selecione uma entidade.";
    }

    $stJs = "  document.frm.inCodTipo.selectedIndex = 0 ;\n
               if( document.getElementById('innerConta') )  document.getElementById('innerConta').innerHTML = '&nbsp;'; \n
               if( document.frm.inCodContas )               document.frm.inCodContas.value = ''; \n
               //document.frm.inCodEntidade.value = ''; \n
               //document.frm.stNomEntidade.selectedIndex = 0 ;\n
               document.frm.inCodTipoProcessado.selectedIndex = 0 ;\n
               if( document.frm.btIncluirContaDebito )      document.frm.btIncluirContaDebito.disabled = false;\n
               if( document.frm.btAlterarContaDebito )      document.frm.btAlterarContaDebito.disabled = true; \n
               document.frm.inCodTipoProcessado.focus(); \n  "   ;

    if (!$stMensagem) {

        if ( ( $_REQUEST['inCodTipo'] ) and ( $_REQUEST['inCodContas'] ) ) {
            $stJs .= addContaDebito ( $_REQUEST['inCodEntidade'],  $_REQUEST['inCodTipo'], $_REQUEST['inCodContas'],  $_REQUEST['innerConta'] );
        }

        if ( ( $_REQUEST['inCodTipoProcessado'] != '' ) and ( $_REQUEST['inCodContaCredito_tipo_7'] ) ) {
            $stJs .= addContaDebito ( $_REQUEST['inCodEntidade'],
                                          $_REQUEST['inCodTipoProcessado'],
                                          $_REQUEST['inCodContaCredito_tipo_7'],
                                          $_REQUEST['innerContaPagarProcessados'] );
            }
        } else {
            $stJs .=   "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }

    break;

case 'delContaDebito' :
        $stJs = delContaDebito ( $_REQUEST['inCodigo'] );
    break;

case 'delContaCredito' :
        $stJs = delContaCredito ( $_REQUEST['inCodigo'] );
    break;

case 'limpar':
    Sessao::write('arContasCredito', array());
    Sessao::write('arContasDebito', array());
    Sessao::write('codigoCredito', 0);
    Sessao::write('codigoDebito' , 0);
    break;

}

if ($stJs) {
    echo $stJs;
}

?>
