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
    * Classe Oculta de Suplementacao
    * Data de Criação   : 10/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Id: OCManterReducao.php 65255 2016-05-05 20:25:44Z michel $

    * Casos de uso: uc-02.01.07
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterSuplementacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');

list($inCodNorma,$stDecreto)  = $request->get('inCodNorma');


$obRegra = new ROrcamentoSuplementacao;
$obRegra->setExercicio( Sessao::getExercicio() );
$obROrcamentoDespesa = new RorcamentoDespesa;
$obROrcamentoDespesa->setExercicio( Sessao::getExercicio());

function montaLabelsNorma($inCodNorma)
{
    global $obRegra;

    $obRegra->obRNorma->setCodNorma( $inCodNorma );
    $obRegra->obRNorma->setExercicio( Sessao::getExercicio() );
    $obRegra->obRNorma->consultar( $rsNorma );

    if ( !$rsNorma->eof() ) {
        $js     = "d.getElementById('stLblNomeNorma').innerHTML = '". $obRegra->obRNorma->getNomeNorma()."';";
    } else {
        $js     = "d.getElementById('stLblNomeNorma').innerHTML = '&nbsp;';";
    }

    return $js;
}

function montaListaReducoes($arRecordSet , $nuVlTotalReducao = 0, $boExecuta = true)
{
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_valor", "NUMERIC_BR" );
        if ( !$rsLista->eof() ) {

            $nuVlTotalReducao = number_format( $nuVlTotalReducao, 2, ',', '.');
            $obLblTotal = new Label;
            $obLblTotal->setRotulo        ( "Total da Reducao" );
            $obLblTotal->setName          ( "nuVlTotalReducao" );
            $obLblTotal->setId            ( "nuVlTotalReducao" );
            $obLblTotal->setValue         ( $nuVlTotalReducao  );

            $obFormulario = new Formulario;
            $obFormulario->addComponente        ( $obLblTotal   );
            $obFormulario->montaInnerHTML();

            $obLista = new Lista;
            $obLista->setTitulo( "Registros de reduções" );
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Reduzido");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Despesa");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Descrição");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Valor");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "cod_reduzido" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "dotacao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "descricao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "vl_valor" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluirDotacaoRedutora('excluirReducao');" );
            $obLista->ultimaAcao->addCampo("1","cod_reduzido");
            $obLista->commitAcao();

            $obLista->montaHTML();

            $stHTML = $obFormulario->getHTML() . $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            $stLista   .= "d.getElementById('spnReducoes').innerHTML = '".$stHTML."'; ";
        } else {
            $stLista   .= "d.getElementById('spnReducoes').innerHTML = ''; ";
            Sessao::remove('arRedutoras');
        }

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto($stLista);
        } else {
            return $stLista;
        }

}

function montaListaSuplementada($arRecordSet, $nuVlTotalSuplementada = 0, $boExecuta = true)
{
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_valor", "NUMERIC_BR" );
        if ( !$rsLista->eof() ) {

            $nuVlTotalSuplementada = number_format( $nuVlTotalSuplementada, 2, ',', '.');
            $obLblTotal = new Label;
            $obLblTotal->setRotulo        ( "Total da Suplementação" );
            $obLblTotal->setName          ( "nuVlTotalSuplementada" );
            $obLblTotal->setId            ( "nuVlTotalSuplementada" );
            $obLblTotal->setValue         ( $nuVlTotalSuplementada  );

            $obFormulario = new Formulario;
            $obFormulario->addComponente        ( $obLblTotal   );
            $obFormulario->montaInnerHTML();

            $obLista = new Lista;
            $obLista->setTitulo( "Registros de suplementações" );
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Reduzido");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Despesa");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Descrição");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Valor");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "cod_reduzido" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "dotacao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "descricao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "vl_valor" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluirDotacaoSuplementada('excluirSuplementada');" );
            $obLista->ultimaAcao->addCampo("1","cod_reduzido");
            $obLista->commitAcao();

            $obLista->montaHTML();

            $stHTML = $obFormulario->getHTML() . $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            $stLista    = "d.getElementById('spnSuplementada').innerHTML = '".$stHTML."'; ";
        } else {
            $stLista    = "d.getElementById('spnSuplementada').innerHTML = ''; ";
            Sessao::remove('arSuplementada');
        }

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto($stLista);
        } else {
            return $stLista;
        }

}

function montaListaReducoesRecurso($arRecordSet , $nuVlTotalReducao = 0)
{
    
        $arRecursosRedutoras = Sessao::read('arRecursosRedutoras');

        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_valor", "NUMERIC_BR" );
        if ( !$rsLista->eof() ) {

            $obFormulario = new Formulario;
        
            foreach ($arRecursosRedutoras as $key => $value) {
                if ( $value['valor_recurso'] > 0) {
                
                    $obLabelTotalRecurso = 'obLabelTotalRecursoRedutora_'.$value['cod_recurso'];
                    $nuValorTotalRecurso = SistemaLegado::formataValorDecimal($value['valor_recurso'], true);
                    $$obLabelTotalRecurso = new Label;
                    $$obLabelTotalRecurso->setRotulo( "Total: ".$value['nom_recurso'] );
                    $$obLabelTotalRecurso->setName  ( "nuVlTotalRecursoRedutora_".$value['cod_recurso'] );
                    $$obLabelTotalRecurso->setId    ( "nuVlTotalRecursoRedutora_".$value['cod_recurso'] );
                    $$obLabelTotalRecurso->setValue ( $nuValorTotalRecurso  );
                
                    $obHdnTotalRecurso = 'obHdhTotalRecursoRedutora_'.$value['cod_recurso'];
                    $$obHdnTotalRecurso = new Hidden();
                    $$obHdnTotalRecurso->setId('nuHdnTotalRecursoRedutora_'.$value['cod_recurso']);
                    $$obHdnTotalRecurso->setName('nuHdnTotalRecursoRedutora_'.$value['cod_recurso']);
                    $$obHdnTotalRecurso->setValue( $value['valor_recurso'] );

                    $obFormulario->addHidden    ( $$obHdnTotalRecurso );
                    $obFormulario->addComponente( $$obLabelTotalRecurso );
                }
            }

            $nuVlTotalReducao = number_format( $nuVlTotalReducao, 2, ',', '.');
            $obLblTotal = new Label;
            $obLblTotal->setRotulo        ( "Total da Reducao" );
            $obLblTotal->setName          ( "nuVlTotalReducao" );
            $obLblTotal->setId            ( "nuVlTotalReducao" );
            $obLblTotal->setValue         ( $nuVlTotalReducao  );
            
            $obFormulario->addComponente        ( $obLblTotal   );
            $obFormulario->montaInnerHTML();

            $obLista = new Lista;
            $obLista->setTitulo( "Registros de reduções" );
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Reduzido");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Despesa");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Descrição");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Valor");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "cod_reduzido" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "dotacao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "descricao" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "vl_valor" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluirDotacaoRedutora('excluirReducao');" );
            $obLista->ultimaAcao->addCampo("1","cod_reduzido");
            $obLista->commitAcao();

            $obLista->montaHTML();

            $stHTML = $obFormulario->getHTML() . $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            $stLista   .= "d.getElementById('spnReducoes').innerHTML = '".$stHTML."'; ";
        } else {
            $stLista   .= "d.getElementById('spnReducoes').innerHTML = ''; ";
            Sessao::remove('arRedutoras');
        }

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto($stLista);
        } else {
            return $stLista;
        }

}

function montaListaSuplementadaRecurso($arRecordSet, $nuVlTotalSuplementada = 0)
{
    $arRecursos = Sessao::read('arRecursos');
    
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao( "vl_valor", "NUMERIC_BR" );    
    if ( !$rsLista->eof() ) {
        
        $obFormulario = new Formulario;
        
        foreach ($arRecursos as $key => $value) {
            if ( $value['valor_recurso'] > 0) {
                
                $obLabelTotalRecurso = 'obLabelTotalRecurso_'.$value['cod_recurso'];
                $nuValorTotalRecurso = SistemaLegado::formataValorDecimal($value['valor_recurso'], true);
                $$obLabelTotalRecurso = new Label;
                $$obLabelTotalRecurso->setRotulo( "Total: ".$value['nom_recurso'] );
                $$obLabelTotalRecurso->setName  ( "nuVlTotalRecurso_".$value['cod_recurso'] );
                $$obLabelTotalRecurso->setId    ( "nuVlTotalRecurso_".$value['cod_recurso'] );
                $$obLabelTotalRecurso->setValue ( $nuValorTotalRecurso  );
                
                $obHdnTotalRecurso = 'obHdhTotalRecurso_'.$value['cod_recurso'];
                $$obHdnTotalRecurso = new Hidden();
                $$obHdnTotalRecurso->setId('nuHdnTotalRecurso_'.$value['cod_recurso']);
                $$obHdnTotalRecurso->setName('nuHdnTotalRecurso_'.$value['cod_recurso']);
                $$obHdnTotalRecurso->setValue( $value['valor_recurso'] );

                $obFormulario->addHidden    ( $$obHdnTotalRecurso );
                $obFormulario->addComponente( $$obLabelTotalRecurso );
            }
        }    

        $nuVlTotalSuplementada = number_format( $nuVlTotalSuplementada, 2, ',', '.');
        $obLblTotal = new Label;
        $obLblTotal->setRotulo        ( "Total da Suplementação" );
        $obLblTotal->setName          ( "nuVlTotalSuplementada" );
        $obLblTotal->setId            ( "nuVlTotalSuplementada" );
        $obLblTotal->setValue         ( $nuVlTotalSuplementada  );
        
        $obFormulario->addComponente        ( $obLblTotal   );
        $obFormulario->montaInnerHTML();

        $obLista = new Lista;
        $obLista->setTitulo( "Registros de suplementações" );
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Reduzido");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Despesa");
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição");
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_reduzido]" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[dotacao]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[descricao]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_valor]" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirDotacaoSuplementada('excluirSuplementada');" );
        $obLista->ultimaAcao->addCampo("1","cod_reduzido");
        $obLista->commitAcao();

        $obLista->montaHTML();

        $stHTML = $obFormulario->getHTML() . $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        $stLista    = "d.getElementById('spnSuplementada').innerHTML = '".$stHTML."'; ";
    } else {
        $stLista    = "d.getElementById('spnSuplementada').innerHTML = ''; ";
        Sessao::remove('arSuplementada');
    }

    return $stLista;
}


function incluirSuplementada(Request $request)
{
    $obRegra = new ROrcamentoSuplementacao;
    $nuVlTotal       = str_replace( '.' , '' , $request->get('nuVlTotal')           );
    $nuVlTotal       = str_replace( ',' ,'.' , $nuVlTotal                    );
    $nuVlSuplementar = str_replace( '.' , '' , $request->get('nuVlDotacaoSuplementada') );
    $nuVlSuplementar = str_replace( ',' ,'.' , $nuVlSuplementar              );
    $nuVlSomatoria   = $nuVlSuplementar;

    $arSuplementada = Sessao::read('arSuplementada');
    $inCount = sizeof($arSuplementada);
    if ($inCount) {
        foreach ($arSuplementada as $value) {
            if ($value['cod_reduzido'] != $request->get('inCodDotacaoSuplementada')) {
                $nuVlSomatoria += $value['vl_valor'];
            } else {
                SistemaLegado::exibeAviso('Esta dotação já está presente na lista',"n_incluir","erro");
                exit;
            }
        }
    }
    if ( bcsub($nuVlSomatoria, $nuVlTotal, 4) <= 0 ) {
        $obRegra->addDespesaSuplementada();
        $obRegra->roUltimoDespesaSuplementada->setCodDespesa( $request->get("inCodDotacaoSuplementada") );
        $obRegra->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
        $obRegra->roUltimoDespesaSuplementada->setExercicio( Sessao::getExercicio() );

        $obRegra->roUltimoDespesaSuplementada->listarDespesaDotacao( $rsDespesa );

        $arSuplementada[$inCount]['num_redutora'] = $inCount+1;
        $arSuplementada[$inCount]['cod_reduzido'] = trim( $rsDespesa->getCampo('cod_despesa') );
        $arSuplementada[$inCount]['dotacao']      = trim( $rsDespesa->getCampo('dotacao')     );
        $arSuplementada[$inCount]['descricao']    = trim( $rsDespesa->getCampo('descricao')   );
        $arSuplementada[$inCount]['vl_valor']     = trim( $nuVlSuplementar                    );

        Sessao::write('arSuplementada',$arSuplementada);
        $stHTML = montaListaSuplementada( $arSuplementada, $nuVlSomatoria );
    } else {
        SistemaLegado::exibeAviso('Valor a suplementar é superior ao permitido',"n_incluir","erro");
    }    
}

function incluirSuplementadaRecurso(Request $request)
{
    $obRegra         = new ROrcamentoSuplementacao;
    $nuVlTotal       = SistemaLegado::formataValorDecimal($request->get('nuVlTotal'), false);
    $nuVlSuplementar = SistemaLegado::formataValorDecimal($request->get('nuVlDotacaoSuplementada'),false);
    $nuVlSomatoria   = $nuVlSuplementar;

    $arSuplementada = Sessao::read('arSuplementada');
    $arRecursos = Sessao::read('arRecursos');
    
    $inCount = sizeof($arSuplementada);
    if ($inCount) {
        foreach ($arSuplementada as $value) {
            if ($value['cod_reduzido'] != $request->get('inCodDotacaoSuplementada')) {
                $nuVlSomatoria += $value['vl_valor'];
            } else {
                SistemaLegado::exibeAviso('Esta dotação já está presente na lista',"n_incluir","erro");
                exit;
            }
        }
    }
    if ( bcsub($nuVlSomatoria, $nuVlTotal, 4) <= 0 ) {
        $obRegra->addDespesaSuplementada();
        $obRegra->roUltimoDespesaSuplementada->setCodDespesa( $request->get("inCodDotacaoSuplementada") );
        $obRegra->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
        $obRegra->roUltimoDespesaSuplementada->setExercicio( Sessao::getExercicio() );

        $obRegra->roUltimoDespesaSuplementada->listarDespesaDotacao( $rsDespesa );
        
        $arSuplementada[$inCount]['num_redutora'] = $inCount+1;
        $arSuplementada[$inCount]['cod_reduzido'] = trim( $rsDespesa->getCampo('cod_despesa') );
        $arSuplementada[$inCount]['dotacao']      = trim( $rsDespesa->getCampo('dotacao')     );
        $arSuplementada[$inCount]['descricao']    = trim( $rsDespesa->getCampo('descricao')   );
        $arSuplementada[$inCount]['cod_recurso']  = trim( $rsDespesa->getCampo('cod_recurso') );
        $arSuplementada[$inCount]['vl_valor']     = trim( $nuVlSuplementar                    );

        foreach ($arRecursos as $key => $value) {
            if ( $value['cod_recurso'] == $arSuplementada[$inCount]['cod_recurso']) {
                $arRecursos[$key]['valor_recurso'] = $arRecursos[$key]['valor_recurso'] + $nuVlSuplementar;
            }
        }
        
        Sessao::write('arRecursos',$arRecursos);
        Sessao::write('arSuplementada',$arSuplementada);
        $stHTML = montaListaSuplementadaRecurso( $arSuplementada, $nuVlSomatoria );
        SistemaLegado::executaFrameOculto($stHTML);
    } else {
        SistemaLegado::exibeAviso('Valor a suplementar é superior ao permitido',"n_incluir","erro");
    }    
}

function incluirReducao(Request $request)
{
    $obRegra         = new ROrcamentoSuplementacao;

    $nuVlTotal       = str_replace( '.' , '' , $request->get('nuVlTotal')           );
    $nuVlTotal       = str_replace( ',' ,'.' , $nuVlTotal                           );
    $nuVlRedutora    = str_replace( '.' , '' , $request->get('nuVlDotacaoRedutora') );
    $nuVlRedutora    = str_replace( ',' ,'.' , $nuVlRedutora                        );
    $nuVlSomatoria   = $nuVlRedutora;

    $arRedutoras = Sessao::read('arRedutoras');

    $inCount = sizeof($arRedutoras);
    if ($inCount) {
        foreach ($arRedutoras as $value) {
            if ($value['cod_reduzido'] != $request->get('inCodDotacaoReducao')) {
                $nuVlSomatoria += $value['vl_valor'];
            } else {
                SistemaLegado::exibeAviso('Esta dotação já está presente na lista',"n_incluir","erro");
                exit;
            }
        }
    }
    if ( bcsub($nuVlSomatoria, $nuVlTotal, 4) <= 0 ) {
        $obRegra->addDespesaReducao();
        $obRegra->roUltimoDespesaReducao->setCodDespesa( $request->get("inCodDotacaoReducao") );
        $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
        $obRegra->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );

        $obRegra->roUltimoDespesaReducao->consultarSaldoDotacao();
        if ( $nuVlRedutora <= $obRegra->roUltimoDespesaReducao->getSaldoDotacao() ) {
            $obRegra->roUltimoDespesaReducao->listarDespesaDotacao( $rsDespesa );

            $arRedutoras[$inCount]['num_redutora'] = $inCount+1;
            $arRedutoras[$inCount]['cod_reduzido'] = trim( $rsDespesa->getCampo('cod_despesa') );
            $arRedutoras[$inCount]['dotacao']      = trim( $rsDespesa->getCampo('dotacao')     );
            $arRedutoras[$inCount]['descricao']    = trim( $rsDespesa->getCampo('descricao')   );
            $arRedutoras[$inCount]['vl_valor']     = trim( $nuVlRedutora                       );

            Sessao::write('arRedutoras',$arRedutoras );
            $stHTML = montaListaReducoes( $arRedutoras, $nuVlSomatoria );
        } else {
            SistemaLegado::exibeAviso('Valor a reduzir é superior ao saldo da dotação',"n_incluir","erro");
        }
    } else {
        SistemaLegado::exibeAviso('Valor a reduzir é superior ao permitido',"n_incluir","erro");
    }
}

function incluirReducaoRecurso(Request $request)
{
    $obRegra         = new ROrcamentoSuplementacao;
    $nuVlTotal       = SistemaLegado::formataValorDecimal($request->get('nuVlTotal'), false);
    $nuVlRedutora    = SistemaLegado::formataValorDecimal($request->get('nuVlDotacaoRedutora'),false);
    $nuVlSomatoria   = $nuVlRedutora;

    $arRedutoras = Sessao::read('arRedutoras');
    $arRecursosRedutoras = Sessao::read('arRecursosRedutoras');

    $inCount = sizeof($arRedutoras);
    if ($inCount) {
        foreach ($arRedutoras as $value) {
            if ($value['cod_reduzido'] != $request->get('inCodDotacaoReducao')) {
                $nuVlSomatoria += $value['vl_valor'];
            } else {
                SistemaLegado::exibeAviso('Esta dotação já está presente na lista',"n_incluir","erro");
                exit;
            }
        }
    }
    if ( bcsub($nuVlSomatoria, $nuVlTotal, 4) <= 0 ) {
        $obRegra->addDespesaReducao();
        $obRegra->roUltimoDespesaReducao->setCodDespesa( $request->get("inCodDotacaoReducao") );
        $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
        $obRegra->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );

        $obRegra->roUltimoDespesaReducao->consultarSaldoDotacao();
        if ( $nuVlRedutora <= $obRegra->roUltimoDespesaReducao->getSaldoDotacao() ) {
            $obRegra->roUltimoDespesaReducao->listarDespesaDotacao( $rsDespesa );

            $arRedutoras[$inCount]['num_redutora'] = $inCount+1;
            $arRedutoras[$inCount]['cod_reduzido'] = trim( $rsDespesa->getCampo('cod_despesa') );
            $arRedutoras[$inCount]['dotacao']      = trim( $rsDespesa->getCampo('dotacao')     );
            $arRedutoras[$inCount]['descricao']    = trim( $rsDespesa->getCampo('descricao')   );
            $arRedutoras[$inCount]['cod_recurso']  = trim( $rsDespesa->getCampo('cod_recurso') );
            $arRedutoras[$inCount]['vl_valor']     = trim( $nuVlRedutora                       );

            foreach ($arRecursosRedutoras as $key => $value) {
                if ( $value['cod_recurso'] == $arRedutoras[$inCount]['cod_recurso']) {
                    $arRecursosRedutoras[$key]['valor_recurso'] = $arRecursosRedutoras[$key]['valor_recurso'] + $nuVlRedutora;
                }
            }   
        
            Sessao::write('arRecursosRedutoras',$arRecursosRedutoras);
            Sessao::write('arRedutoras',$arRedutoras );
            $stHTML = montaListaReducoesRecurso( $arRedutoras, $nuVlSomatoria );
            SistemaLegado::executaFrameOculto($stHTML);
        } else {
            SistemaLegado::exibeAviso('Valor a reduzir é superior ao saldo da dotação',"n_incluir","erro");
        }
    } else {
        SistemaLegado::exibeAviso('Valor a reduzir é superior ao permitido',"n_incluir","erro");
    }
}

switch ($stCtrl) {

    case 'norma':
        $js  = montaLabelsNorma( $inCodNorma );
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'reducoes':
        $js  = montaListaReducoes( $arRecordReducoes );
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'incluirReducao':
        $stConfiguracao = SistemaLegado::pegaConfiguracao('suplementacao_rigida_recurso', 8, Sessao::getExercicio(), $boTransacao);
        if ( $stConfiguracao == 'sim' ) {
            $arRecursosRedutoras = Sessao::read('arRecursosRedutoras');
            if( count($arRecursosRedutoras) == 0){
                include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
                $obTOrcamentoRecurso = new TOrcamentoRecurso;
                $obTOrcamentoRecurso->setDado('exercicio', Sessao::getExercicio() );
                $obTOrcamentoRecurso->recuperaRelacionamento( $rsListaRecursos, '', '', $boTransacao );
                foreach ($rsListaRecursos->getElementos() as $key => $value) {
                    $arRecursosRedutoras[$key]['cod_recurso']   = $value['cod_recurso'];
                    $arRecursosRedutoras[$key]['nom_recurso']   = $value['nom_recurso'];
                    $arRecursosRedutoras[$key]['valor_recurso'] = 0.00;
                }
                Sessao::write('arRecursosRedutoras', $arRecursosRedutoras);
            }
            incluirReducaoRecurso($request);
        }else{
            incluirReducao($request);
        }

    break;

    case 'excluirReducao':
        $stConfiguracao = SistemaLegado::pegaConfiguracao('suplementacao_rigida_recurso', 8, Sessao::getExercicio(), $boTransacao);
        if ( $stConfiguracao == 'sim' ) {
            $arTEMP    = array();
            $inCount   = 0;
            $nuVlTotal = 0;
            $arRecursos = Sessao::read('arRedutoras');
            $arRecursosRedutoras = Sessao::read('arRecursosRedutoras');            
            foreach ($arRecursos as $key => $value) {
                if ( ($value['cod_reduzido']) != $request->get('inCodDotacaoReducao') ) {
                    $arTEMP[$inCount]['cod_reduzido'] = $value['cod_reduzido'];
                    $arTEMP[$inCount]['dotacao']      = $value['dotacao'];
                    $arTEMP[$inCount]['descricao']    = $value['descricao'];
                    $arTEMP[$inCount]['cod_recurso']  = $value['cod_recurso'];
                    $arTEMP[$inCount]['vl_valor']     = $value['vl_valor'];
                    $nuVlTotal                       += $value['vl_valor'];
                    $inCount++;
                }else{
                    foreach ($arRecursosRedutoras as $key2 => $value2) {
                        if ( $value2['cod_recurso'] == $value['cod_recurso'] ) {
                            $arRecursosRedutoras[$key2]['valor_recurso'] = $arRecursosRedutoras[$key2]['valor_recurso'] - $value['vl_valor'];
                        }
                    }
                }
            }            
            $arRecursos = $arTEMP;            
            Sessao::write('arRecursosRedutoras',$arRecursosRedutoras);
            Sessao::write('arRedutoras',$arRecursos);
            $stHtml = montaListaReducoesRecurso( $arRecursos, $nuVlTotal );
            SistemaLegado::executaFrameOculto($stHtml);
        }else{

            $arTEMP    = array();
            $inCount   = 0;
            $nuVlTotal = 0;
            $arRedutoras = Sessao::read('arRedutoras');
            foreach ($arRedutoras as $value) {
                if ( ($value['cod_reduzido']) != $request->get('inCodDotacaoReducao') ) {
                    $arTEMP[$inCount]['cod_reduzido'] = $value['cod_reduzido'];
                    $arTEMP[$inCount]['dotacao']      = $value['dotacao'];
                    $arTEMP[$inCount]['descricao']    = $value['descricao'];
                    $arTEMP[$inCount]['vl_valor']     = $value['vl_valor'];
                    $nuVlTotal                       += $arTEMP[$inCount]['vl_valor'];
                    $inCount++;
                }
            }
            Sessao::write('arRedutoras',$arTEMP);
            montaListaReducoes( $arTEMP, $nuVlTotal,true);
        }
    break;

    case 'incluirSuplementada':
        $stConfiguracao = SistemaLegado::pegaConfiguracao('suplementacao_rigida_recurso', 8, Sessao::getExercicio(), $boTransacao);
        if ( $stConfiguracao == 'sim' ) {
            $arRecursos = Sessao::read('arRecursos');
            if( count($arRecursos) == 0){
                include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php" );
                $obTOrcamentoRecurso = new TOrcamentoRecurso;
                $obTOrcamentoRecurso->setDado('exercicio', Sessao::getExercicio() );
                $obTOrcamentoRecurso->recuperaRelacionamento( $rsListaRecursos, '', '', $boTransacao );
                foreach ($rsListaRecursos->getElementos() as $key => $value) {
                    $arRecursos[$key]['cod_recurso']   = $value['cod_recurso'];
                    $arRecursos[$key]['nom_recurso']   = $value['nom_recurso'];
                    $arRecursos[$key]['valor_recurso'] = 0.00;
                }
                Sessao::write('arRecursos', $arRecursos);
            }
            incluirSuplementadaRecurso($request);
        }else{
            incluirSuplementada($request);
        }

    break;
    case 'excluirSuplementada':
        $stConfiguracao = SistemaLegado::pegaConfiguracao('suplementacao_rigida_recurso', 8, Sessao::getExercicio(), $boTransacao);
        if ( $stConfiguracao == 'sim' ) {
            $arTEMP    = array();
            $inCount   = 0;
            $nuVlTotal = 0;
            $arSuplementada = Sessao::read('arSuplementada');
            $arRecursos = Sessao::read('arRecursos');
            foreach ($arSuplementada as $key => $value) {
                if ( ($value['cod_reduzido']) != $request->get('inCodDotacaoSuplementada') ) {
                    $arTEMP[$inCount]['cod_reduzido'] = $value['cod_reduzido'];
                    $arTEMP[$inCount]['dotacao']      = $value['dotacao'];
                    $arTEMP[$inCount]['descricao']    = $value['descricao'];
                    $arTEMP[$inCount]['cod_recurso']  = $value['cod_recurso'];
                    $arTEMP[$inCount]['vl_valor']     = $value['vl_valor'];
                    $nuVlTotal                       += $value['vl_valor'];
                    $inCount++;
                }else{
                    foreach ($arRecursos as $key2 => $value2) {
                        if ( $value2['cod_recurso'] == $value['cod_recurso'] ) {
                            $arRecursos[$key2]['valor_recurso'] = $arRecursos[$key2]['valor_recurso'] - $value['vl_valor'];
                        }
                    }
                }
            }
            $arSuplementada = $arTEMP;            
            Sessao::write('arRecursos',$arRecursos);
            Sessao::write('arSuplementada',$arSuplementada);
            $stHtml = montaListaSuplementadaRecurso( $arTEMP, $nuVlTotal );
            SistemaLegado::executaFrameOculto($stHtml);
        }else{
            $arTEMP    = array();
            $inCount   = 0;
            $nuVlTotal = 0;
            $arSuplementada = Sessao::read('arSuplementada');
            foreach ($arSuplementada as $value) {
                if ( ($value['cod_reduzido']) != $request->get('inCodDotacaoSuplementada') ) {
                    $arTEMP[$inCount]['cod_reduzido'] = $value['cod_reduzido'];
                    $arTEMP[$inCount]['dotacao']      = $value['dotacao'];
                    $arTEMP[$inCount]['descricao']    = $value['descricao'];
                    $arTEMP[$inCount]['vl_valor']     = $value['vl_valor'];
                    $nuVlTotal                       += $value['vl_valor'];
                    $inCount++;
                }
            }
            Sessao::write('arSuplementada',$arSuplementada);        
            montaListaSuplementada( $arTEMP, $nuVlTotal,true );
        }

    break;
    case 'buscaDespesaReducao':
        if (($request->get("inCodDotacaoReducao", '') != "") && ($request->get('inCodEntidade', '') != "")) {

            $obRegra->addDespesaReducao();
            $obRegra->roUltimoDespesaReducao->setCodDespesa( $request->get("inCodDotacaoReducao") );
            $obRegra->roUltimoDespesaReducao->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
            $obRegra->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );
            $obRegra->roUltimoDespesaReducao->listarDespesa( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo( "descricao" );

            if (!$stNomDespesa) {
                $js  = 'f.inCodDotacaoReducao.value = "";';
                $js .= 'f.inCodDotacaoReducao.focus();';
                $js .= 'd.getElementById("stNomDotacaoRedutora").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$request->get("inCodDotacaoReducao").")','form','erro','".Sessao::getId()."');";
            } else {
                $js  = 'd.getElementById("stNomDotacaoRedutora").innerHTML = "'.$stNomDespesa.'";';
            }
        } else {
            $js  = 'd.getElementById("stNomDotacaoRedutora").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaDespesaSuplementada':
        if (($request->get("inCodDotacaoSuplementada", '') != "") && ($request->get('inCodEntidade', '') != "")) {
            $obRegra->addDespesaSuplementada();
            $obRegra->roUltimoDespesaSuplementada->setCodDespesa( $request->get("inCodDotacaoSuplementada") );
            $obRegra->roUltimoDespesaSuplementada->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
            $obRegra->roUltimoDespesaSuplementada->setExercicio( Sessao::getExercicio() );
            $obRegra->roUltimoDespesaSuplementada->listarDespesa( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo( "descricao" );

            if (!$stNomDespesa) {
                $js  = 'f.inCodDotacaoSuplementada.value = "";';
                $js .= 'f.inCodDotacaoSuplementada.focus();';
                $js .= 'd.getElementById("stNomDotacaoSuplementada").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$request->get("inCodDotacaoSuplementada").")','form','erro','".Sessao::getId()."');";
            } else {
                $js  = 'd.getElementById("stNomDotacaoSuplementada").innerHTML = "'.$stNomDespesa.'";';
            }
        } else {
            $js  = 'd.getElementById("stNomDotacaoSuplementada").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaDespesaSuplementadaEspecial':

    if ($request->get("inCodDotacaoSuplementada", '') != "" &&  $request->get('inCodEntidade', '') != "") {

        $obROrcamentoDespesa->setCodDespesa($request->get("inCodDotacaoSuplementada") );
        $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($request->get("inCodEntidade") );
        $obROrcamentoDespesa->listarDespesaCredEspecial($rsEspDespesa);
        $stNomDespesa = $rsEspDespesa->getCampo("descricao");

        if ($rsEspDespesa->getCampo( "vl_original" )) {
            if ( $rsEspDespesa->getCampo( "vl_original" ) != 0.00 ) {
                $js = "alertaAviso('@Dotação não é um credito especial.','form','erro','".Sessao::getId()."');";
                $js.= 'f.inCodDotacaoSuplementada.value = "";';
                $js.= 'f.inCodDotacaoSuplementada.focus();';
                $js.= 'd.getElementById("stNomDotacaoSuplementada").innerHTML ="&nbsp;";';
            } else {
                $js .= 'd.getElementById("stNomDotacaoSuplementada").innerHTML = "'.$stNomDespesa.'";';
            }
        } else {
            $js = "alertaAviso('@Dotação não é um credito especial.','form','erro','".Sessao::getId()."');";
            $js.= 'f.inCodDotacaoSuplementada.value = "";';
            $js.= 'f.inCodDotacaoSuplementada.focus();';
            $js.= 'd.getElementById("stNomDotacaoSuplementada").innerHTML ="&nbsp;";';
        }
    } else $js .= 'd.getElementById("stNomDotacaoSuplementada").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

    case 'limparListas':
        Sessao::remove('arSuplementada');
        Sessao::remove('arRedutoras');
        Sessao::remove('arRecursos');
        Sessao::remove('arRecursosRedutoras');        
    break;
        case 'buscaNorma':
        if ($request->get('inCodNorma')) {
            $obRegra->obRNorma->setCodNorma( $request->get('inCodNorma') );
            $obRegra->obRNorma->setExercicio( Sessao::getExercicio() );
            $obErro = $obRegra->obRNorma->consultar( $rsRecordSet );
            if ( !$obErro->ocorreu() ) {
                if ( $obRegra->obRNorma->getNomeNorma() != NULL ) {
                    $stNorma  = $obRegra->obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obRegra->obRNorma->getNumNorma();
                    $stNorma .= '/'.$obRegra->obRNorma->getExercicio().' - '.$obRegra->obRNorma->getNomeNorma();
                     $js = 'd.getElementById("stNomTipoNorma").innerHTML = "'.$stNorma.'";';
                } else {
                    $js  = 'f.inCodNorma.value = "";';
                    $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodNorma.focus();';
                    $js .= 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
                    $js .= "alertaAviso('@Valor inválido. (".$request->get("inCodNorma").")','form','erro','".Sessao::getId()."');";
                }
            }
        } else {
            $js = 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'validaValorLimiteSuplementacao':
        if ($request->get('inCodEntidade') != '') {            
            include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoSuplementacao.class.php";
            $obTOrcamentoSuplementacao = new TOrcamentoSuplementacao;

            //Caso a suplementação a ser realizada somada as demais suplementações já realizadas
            //ultrapassar o montante do percentual definido na LOA, esta suplementação nao podera ser realizada utilizando a lei da LOA
            $query = "SELECT SUM(vl_original) as valor FROM orcamento.despesa WHERE exercicio = '".Sessao::getExercicio()."' AND cod_entidade = ".$request->get('inCodEntidade')."";
            $nuValorTotalOrcamento = SistemaLegado::pegaValor($query,'valor');
            $nuPorcentagemLimite   = SistemaLegado::pegaConfiguracao('limite_suplementacao_decreto',8,Sessao::getExercicio(), $boTransacao);
            $nuPorcentagemLimite   = str_replace(',','.',$nuPorcentagemLimite);

            $nuValorLimiteSuplementacoes = round(($nuValorTotalOrcamento*$nuPorcentagemLimite)/100,2);

            $stFiltro  = " AND suplementacao.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= " AND despesa.cod_entidade IN (".$request->get('inCodEntidade').") ";
            $obTOrcamentoSuplementacao->recuperaValorTotalSuplementado($rsValorSuplementado, $stFiltro, '',$boTransacao);

            if ( $rsValorSuplementado->getNumLinhas() > 0 ) {
                $nuValorTotal = SistemaLegado::formataValorDecimal($request->get('nuVlTotal'),false);
                $nuValorTotalSuplementado = $rsValorSuplementado->getCampo('valor_suplementado') + $nuValorTotal;
                if ( $nuValorTotalSuplementado > $nuValorLimiteSuplementacoes) {
                    $nuValorDisponivel = $nuValorLimiteSuplementacoes - $rsValorSuplementado->getCampo('valor_suplementado');
                    $nuValorDisponivel = SistemaLegado::formataValorDecimal($nuValorDisponivel, true);
                    $stMensagem = "Atenção! O valor a ser Suplementado, supera o limite estabelecido. Disponivel para Suplementar: R$ ".$nuValorDisponivel.".";
                    $stJs = " jq_('#nuVlTotal').val('0,00'); ";
                    SistemaLegado::executaFrameOculto($stJs);
                    SistemaLegado::exibeAviso($stMensagem,"aviso","aviso");
                }
            }
        }else{
            $stJs = " jq_('#nuVlTotal').val('0,00'); jq_('#inCodEntidade').focus(); ";
            $stMensagem = "Selecione uma Entidade antes de acrescentar o Valor Total.";
            SistemaLegado::executaFrameOculto($stJs);
            SistemaLegado::exibeAviso($stMensagem,"aviso","aviso");
        }
    break;

}

?>
