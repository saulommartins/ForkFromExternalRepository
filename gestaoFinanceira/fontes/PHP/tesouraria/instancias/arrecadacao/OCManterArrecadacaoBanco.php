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
    * Paginae Oculta para funcionalidade Manter Arrecadação via Banco
    * Data de Criação   : 01/03/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Rodrigo S. Rodrigues

    * @ignore

    * $Id: OCManterArrecadacaoBanco.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.33

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLoteArrecadacao.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoBanco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JSManterArrecadacaoReceita.js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );

$stJs = '';

switch ($stCtrl) {
    case 'buscaBoletim':
        if ($_REQUEST['inCodEntidade']) {
            require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
            $obISelectBoletim = new ISelectBoletim;
        $obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
        $obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
        $obISelectBoletim->obEvento->setOnChange ( "executaFuncaoAjax(
                                                                          'buscaLote',
                                                                          '&inCodEntidade='+$('inCodEntidade').value
                                                                          +'&stAcao='+$('stAcao').value
                                                                           +'&inCodBoletim='+$('inCodBoletim').value+'',
                                                                          false);");
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obISelectBoletim );
            $obFormulario->montaInnerHtml();
            $stHTML = $obFormulario->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

            $stJs .= " $('spnBoletim').innerHTML = '".$stHTML."';\n";

        if ( !strstr( $stHTML ,  "Não há boletins abertos para esta entidade") ) {
            // poderia ter usado o montaParametrosGET, mas ele nao acha o valor do boletim, somente puxando antes.
            $stJs .= " var stLink = '&inCodEntidade='+$('inCodEntidade').value+'&stAcao='+$('stAcao').value+'&inCodBoletim='+$('inCodBoletim').value+'' ;\n";
            $stJs .= " if ( $('inCodBoletim').length == 1) {executaFuncaoAjax( 'buscaLote', stLink, false ); }";
        } else {
            $stJs .= " $('spnLote').innerHTML = '';\r\n";
        }
        $stJs .= " $('spnLote').innerHTML = '';\r\n";
        } else {
            $stJs .= " $('spnBoletim').innerHTML = '';\r\n";
            $stJs .= " $('spnLote').innerHTML = '';\r\n";
        }

    echo $stJs;
    break;

    case 'buscaLote':
        if ($_REQUEST['inCodBoletim']) {
            $arBoletim = explode( ":" , $_REQUEST['inCodBoletim'] );
            if ($_REQUEST['stAcao'] == 'incluir') {
                include_once(CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLoteArrecadacao.class.php");

                //instanciar uma classe de mapeamento que tenha a consulta que retorne os valores da tabela
                //deve montar um filtro com os valores que estão vindo do dos combos do formulário. Sacou?

                $stFiltro .= " AND boletim.EXERCICIO = '".$arBoletim[2]."' ";
                $stFiltro .= " AND boletim.COD_ENTIDADE = ".$arBoletim[3];
                $stFiltro .= " AND boletim.COD_BOLETIM = ".$arBoletim[0];

                $stOrdem  = " order by lote.cod_lote asc";

                $obTTesourariaBoletimLote = new TTesourariaBoletimLoteArrecadacao();
                $obTTesourariaBoletimLote->recuperaBoletimLote($rsLista , $stFiltro , $stOrdem);
            } else {
                include_once(CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLoteArrecadacaoEstornado.class.php");
                $obTTesourariaBoletimLoteArrecadacaoEstornado = new TTesourariaBoletimLoteArrecadacaoEstornado();
                $obTTesourariaBoletimLoteArrecadacaoEstornado->setDado('exercicio', $arBoletim[2]);
                $obTTesourariaBoletimLoteArrecadacaoEstornado->setDado('cod_entidade', $arBoletim[3]);
                $obTTesourariaBoletimLoteArrecadacaoEstornado->setDado('cod_boletim', $arBoletim[0]);

                $stOrdem  = " order by lote.cod_lote asc";

                $obTTesourariaBoletimLoteArrecadacaoEstornado->recuperaBoletimLoteEstornado($rsLista , $stFiltro , $stOrdem);
            }

            Sessao::write('rsLotes', serialize($rsLista));

            if ($_REQUEST['stAcao'] == 'incluir') {
                $obTable = new TableTree;
                $obTable->setArquivo( CAM_GF_TES_INSTANCIAS . 'arrecadacao/OCManterArrecadacaoBanco.php');
                $obTable->setParametros( array( "cod_lote" , "exercicio" , "ok_credito" , "ok_banco", "ok_conta_corrente", "ok_plano_conta") );
                $obTable->setComplementoParametros( "stCtrl=detalharLote");
            } else {
                $obTable = new Table;
            }

            $obTable->setRecordset( $rsLista );
            $obTable->setSummary('Lotes');
            //$obTable->setConditional( 'ok', array( 'f' ) , '#fee' );

            $obTable->Head->addCabecalho( 'Lote'      , 10 );
            $obTable->Head->addCabecalho( 'Data'      , 15  );
            $obTable->Head->addCabecalho( 'Banco'     , 35  );
            $obTable->Head->addCabecalho( 'Agência'   , 35  );
            if ($_REQUEST['stAcao'] == 'incluir') {
                $obTable->Head->addCabecalho( 'Arrecadar' , 5  );
            } else {
                $obTable->Head->addCabecalho( 'Estornar' , 5 );
            }

            $obTable->Body->addCampo( 'cod_lote'    , 'C' );
            $obTable->Body->addCampo( 'data_br'   , 'C' );
            $obTable->Body->addCampo( 'banco'   , 'C' );
            $obTable->Body->addCampo( 'agencia' , 'C' );

            $obChkArrecadar = new CheckBox;
            $obChkArrecadar->setName('boArrecadar_[cod_lote]_[exercicio]');
            $obChkArrecadar->setId('boArrecadar_[cod_lote]_[exercicio]');

            //$obTable->Body->addCampo( $obChkArrecadar);
            $obTable->Body->addComponente ( $obChkArrecadar , 'ok' );

            $obTable->montaHTML();

            $stHTML = $obTable->getHtml();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

            $stJs = "d.getElementById('spnLote').innerHTML='".$stHTML."';\r\n";
        } else {
            $stJs = "d.getElementById('spnLote').innerHTML=' ';\r\n";
        }
       echo $stJs ;
    break;
case 'detalharLote':

    require_once( CAM_GT_ARR_MAPEAMENTO . 'TARRLote.class.php');
    $obTLote = new TARRLote();
    $stFiltro  = " where lote.cod_lote = " . $_REQUEST['cod_lote'];
    $stFiltro .= "   and lote.exercicio = '" . $_REQUEST['exercicio'] . "' ";
    $obTLote->executaRecupera("montaRecuperaDetalheLoteTesouraria",$rsLote,$stFiltro,$stOrder,$boTransacao);

    $obLabelDataLote = new Label();
    $obLabelDataLote->setRotulo('Data do Lote');
    $obLabelDataLote->setValue ( $rsLote->getCampo('data_lote') );

    $obLabelExercicio= new Label();
    $obLabelExercicio->setRotulo('Exercício');
    $obLabelExercicio->setValue ( $rsLote->getCampo('exercicio') );

    $obLabelPagInconsistente= new Label();
    $obLabelPagInconsistente->setRotulo('Soma Inconsistências');
    $obLabelPagInconsistente->setValue ( number_format((float) $rsLote->getCampo('soma_inconsistencia') ,2,',','.')  );

    $obLabelTotalInconsistente= new Label();
    $obLabelTotalInconsistente->setRotulo('Número de Inconsistências');
    $obLabelTotalInconsistente->setValue ( $rsLote->getCampo('conta_inconsistencia') );

    $obLabelPagamentos= new Label();
    $obLabelPagamentos->setRotulo('Soma Pagamentos');
    $obLabelPagamentos->setValue ( number_format((float) $rsLote->getCampo('soma_pagamentos') ,2,',','.') );

    $obLabelTotalPagamentos= new Label();
    $obLabelTotalPagamentos->setRotulo('Número de Pagamentos ');
    $obLabelTotalPagamentos->setValue ( $rsLote->getCampo('conta_pagamentos') );

    $stTotalLote  = $rsLote->getCampo('soma_pagamentos') + $rsLote->getCampo('soma_inconsistencia');

    $obLabelTotalLote= new Label();
    $obLabelTotalLote->setRotulo('Valor Total do Lote ');
    $obLabelTotalLote->setValue ( number_format( $stTotalLote , 2,',','.') );

    $obLabelSituacao= new Label();
    $obLabelSituacao->setRotulo('Situação');
    if( $_REQUEST['ok_credito'] == 't'
        && $_REQUEST['ok_banco'] == 't'
        && $_REQUEST['ok_conta_corrente'] == 't'
        && $_REQUEST['ok_plano_conta'] == 't'
        && $rsLote->getCampo('situacao_plano_banco') == 't'
      )
    {
        $obLabelSituacao->setValue ( "Lote pronto para arrecadar" );
    } else {
        $stMens="";
    if ($_REQUEST['ok_credito'] == 'f') {
        $stMens=" - Lote contém pagamentos com Créditos e/ou Acréscimos não vinculados a Contabilidade.";
    }
    if ($_REQUEST['ok_plano_conta'] == 'f') {
        if($stMens)
            $stMens .= "<br>";
        $stMens.=" - A conta bancária não pertence ao grupo 1.1.1.1.2.00.00.00.00.00 .";
    }
    if ($rsLote->getCampo('situacao_plano_banco') == 0) {
        if($stMens)
            $stMens .= "<br>";
        $stMens.=" - Agência do Lote não possui vínculo no Plano de Contas.";
    } elseif ($rsLote->getCampo('situacao_plano_banco') > 1) {
            if($stMens)
                $stMens .= "<br>";
        $stMens.=" - Lote contém créditos onde a conta corrente está vinculada a mais de uma conta de banco.";
    }
    if ($_REQUEST['ok_conta_corrente'] == 'f') {
        if($stMens)
            $stMens .= "<br>";
        $stMens.=" - Lote contém pagamentos com Créditos e/ou Acréscimos não vinculados a uma Conta Corrente .";
    }
        $obLabelSituacao->setValue ( $stMens );
    }

    $obFormulario = new Formulario();
    $obFormulario->addComponente( $obLabelSituacao );
    $obFormulario->addComponente( $obLabelDataLote );
    $obFormulario->addComponente( $obLabelExercicio );
    $obFormulario->addComponente( $obLabelTotalInconsistente );
    $obFormulario->addComponente( $obLabelPagInconsistente );
    $obFormulario->addComponente( $obLabelTotalPagamentos );
    $obFormulario->addComponente( $obLabelPagamentos );
    $obFormulario->addComponente( $obLabelTotalLote );
    $obFormulario->show();

    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLoteArrecadacao.class.php");
    $stFiltro  = " where lote.cod_lote= ".$_REQUEST['cod_lote'];
    $stFiltro .= "   and lote.exercicio= '".$_REQUEST['exercicio']."' ";

    $obTTesourariaBoletimLote = new TTesourariaBoletimLoteArrecadacao();
    $obTTesourariaBoletimLote->recuperaCreditosLote( $rsLista , $stFiltro );

    $obTable = new TableTree;
    $obTable->setArquivo( CAM_GF_TES_INSTANCIAS . 'arrecadacao/OCManterArrecadacaoBanco.php');
    $obTable->setParametros( array( "cod_credito" , "cod_especie" , "cod_genero" , "cod_natureza" , "ok") );
    $obTable->setComplementoParametros( "stCtrl=detalharCreditoLote");
    $obTable->setRecordset( $rsLista );
    $obTable->setSummary('Lista de Créditos do Lote');
    $obTable->setConditional( 'situacao_acrescimo', array( 'f' ) , '#fee' );

    $obTable->Head->addCabecalho( 'Crédito'      , 50 );
    $obTable->Head->addCabecalho( 'Receita'      , 30 );
    $obTable->Head->addCabecalho( 'Ocorrências'  , 20  );

    $obTable->Body->addCampo( '[cod_credito].[cod_especie].[cod_genero].[cod_natureza] - [descricao_credito]'    , 'E' );
    $obTable->Body->addCampo( 'receita'   , 'C' );
    $obTable->Body->addCampo( 'contador'   , 'C' );

    $obTable->montaHTML();

        echo $obTable->getHtml();

    break;
case "detalharCreditoLote":
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaBoletimLoteArrecadacao.class.php");
    $stFiltro  = " where credito_acrescimo.cod_credito= ".$_REQUEST['cod_credito'];
    $stFiltro .= "   and credito_acrescimo.cod_especie= ".$_REQUEST['cod_especie'];
    $stFiltro .= "   and credito_acrescimo.cod_genero= ".$_REQUEST['cod_genero'];
    $stFiltro .= "   and credito_acrescimo.cod_natureza= ".$_REQUEST['cod_natureza'];

    $obTTesourariaBoletimLote = new TTesourariaBoletimLoteArrecadacao();
    $obTTesourariaBoletimLote->executaRecupera("montaRecuperaAcrescimosCreditoLote",$rsLista,$stFiltro,$stOrder);

    $obTable = new Table;
    $obTable->setRecordset( $rsLista );
    $obTable->setSummary('Lista de Acréscimos do Crédito');
    $obTable->setConditional( 'situacao', array( 'Não Ok' ) , '#fee' );

    $obTable->Head->addCabecalho( 'Acréscimo'      , 50 );
    $obTable->Head->addCabecalho( 'Receita'   , 30  );
    $obTable->Head->addCabecalho( 'Situação'  , 20  );

    $obTable->Body->addCampo( '[cod_acrescimo] - [descricao_acrescimo]'    , 'E' );
    $obTable->Body->addCampo( 'receita'   , 'C' );
    $obTable->Body->addCampo( 'situacao'   , 'C' );

    $obTable->montaHTML();

    echo $obTable->getHtml();

    break;
}
