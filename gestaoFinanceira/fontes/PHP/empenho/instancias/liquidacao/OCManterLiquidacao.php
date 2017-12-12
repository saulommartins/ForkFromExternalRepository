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
    * Classe Oculta de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore


    $Id: OCManterLiquidacao.php 65678 2016-06-08 19:16:58Z jean $

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2008-01-09 11:50:24 -0200 (Qua, 09 Jan 2008) $

    * Casos de uso: uc-02.03.24, uc-02.03.04, uc-02.03.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php"     );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaLista($arRecordSet , $boExecuta = true)
{
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao( "vl_total", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_empenhado_anulado", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_liquidado_anulado", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_liquidado", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_a_liquidar", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_liquidado_real", "NUMERIC_BR" );

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenhado");
    $obLista->ultimoCabecalho->setWidth( 13 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Liquidado");
    $obLista->ultimoCabecalho->setWidth( 13 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("A Liquidar");
    $obLista->ultimoCabecalho->setWidth( 26 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_item" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_total" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_liquidado_real" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();

    // Define Objeto Numerico para Valor
    $obTxtValor = new Numerico;
    $obTxtValor->setName     ( "nuValor_[num_item]_" );
    $obTxtValor->setAlign    ( "RIGHT");
    $obTxtValor->setTitle    ( "" );
    $obTxtValor->setMaxLength( 19 );
    $obTxtValor->setSize     ( 21 );
    $obTxtValor->setNegativo ( false );
    $obTxtValor->setNull     ( false );
    $obTxtValor->setValue    ( "vl_a_liquidar" );
    $obTxtValor->obEvento->setOnChange( "montaParametrosGET('totalizaItens');");

    if ($_REQUEST['boAdiantamento']) {
        $obTxtValor->setReadOnly( true );
    }

    $obLista->addDadoComponente( $obTxtValor );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\'",$stHTML );
    $stHTML = str_replace( "\\\'","\'",$stHTML );

    foreach ($arRecordSet as $value) {
        $nuVlTotalEmp += $value['vl_total'];
        $nuVlTotalaLiq += $value['vl_a_liquidar'];
        $nuVlTotalLiq += $value['vl_liquidado'];
        $nuVlTotalLiqAnulado += $value['vl_liquidado_anulado'];
    }

    $nuVlTotalSaldo = $nuVlTotalEmp - ($nuVlTotalLiq + $nuVlTotalaLiq - $nuVlTotalLiqAnulado);
    $nuVlTotalSaldo = number_format($nuVlTotalSaldo,2,',','.');

    $nuVlTotalEmp = number_format($nuVlTotalEmp,2,',','.');
    $nuVlTotalaLiq = number_format($nuVlTotalaLiq,2,',','.');

    if ($boExecuta) {
        $stJS.= " d.getElementById('spnLista').innerHTML = '".$stHTML."';                   ";
        $stJS.= " d.getElementById('nuValorTotalEmp').innerHTML = '".$nuVlTotalEmp."';      ";
        $stJS.= " f.vlTotalEmpenho.value = '".$nuVlTotalEmp."';                             ";
        $stJS.= " f.vlTotalLiquidado.value = '".$nuVlTotalLiq."';                           ";
        $stJS.= " f.vlTotalLiquidadoAnulado.value = '".$nuVlTotalLiqAnulado."';             ";
        $stJS.= " d.getElementById('nuValorTotalSaldo').innerHTML = '".$nuVlTotalSaldo."';  ";
        $stJS.= " d.getElementById('nuValorTotal').innerHTML = '".$nuVlTotalaLiq."';         ";
        $stJS.= " f.Ok.disabled = false;                                                    ";
        
        if((SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()))==11){
            $stJS .= "d.getElementById('nuTotalNf').value = '".$nuVlTotalaLiq."';";
        }

        if($_REQUEST['boAdiantamento'])
            $stJS.= " alertaAvisoTelaPrincipal('@Este empenho é de adiantamentos/subvenções: a Liquidação não poderá ser parcial.','form','erro','".Sessao::getId()."');";

        SistemaLegado::executaiFrameOculto( $stJS );
    } else {
        return $stHTML;
    }
}

function montaCamposTipoDocumentoBilhete()
{
    $obTxtNumero = new TextBox;
    $obTxtNumero->setName     ('stNumero');
    $obTxtNumero->setId       ('stNumero');
    $obTxtNumero->setValue    ('');
    $obTxtNumero->setRotulo   ('Número');
    $obTxtNumero->setTitle    ('Informe o Número');
    $obTxtNumero->setInteiro  (true);
    $obTxtNumero->setMaxLength(15);
    $obTxtNumero->setSize     (15);

    $obDataEmissao = new Data;
    $obDataEmissao->setName  ('dtEmissao');
    $obDataEmissao->setId    ('dtEmissao');
    $obDataEmissao->setRotulo('Informe a Data de Emissão');

    $obDataSaida = new Data;
    $obDataSaida->setName  ('dtSaida');
    $obDataSaida->setId    ('dtSaida');
    $obDataSaida->setRotulo('Informe a Data de Saída');

    $obHoraSaida = new Hora;
    $obHoraSaida->setName  ('hrSaida');
    $obHoraSaida->setId    ('hrSaida');
    $obHoraSaida->setRotulo('Informe a Hora de Saída');

    $obTxtDestino = new TextBox;
    $obTxtDestino->setName   ('stDestino');
    $obTxtDestino->setId     ('stDestino');
    $obTxtDestino->setValue  ('');
    $obTxtDestino->setRotulo ('Destino');
    $obTxtDestino->setTitle  ('Informe o Destino');
    $obTxtDestino->setMaxLength(25);
    $obTxtDestino->setSize     (25);

    $obDataChegada = new Data;
    $obDataChegada->setName  ('dtChegada');
    $obDataChegada->setId    ('dtChegada');
    $obDataChegada->setRotulo('Informe a Data de Chegada');

    $obHoraChegada = new Hora;
    $obHoraChegada->setName  ('hrChegada');
    $obHoraChegada->setId    ('hrChegada');
    $obHoraChegada->setRotulo('Informe a Hora de Chegada');

    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName         ('stMotivo');
    $obTxtMotivo->setId           ('stMotivo');
    $obTxtMotivo->setValue        ('');
    $obTxtMotivo->setRotulo       ('Motivo');
    $obTxtMotivo->setTitle        ('Informe o Motivo');
    $obTxtMotivo->setCols         (100            );
    $obTxtMotivo->setRows         (3              );
    $obTxtMotivo->setMaxCaracteres(120);

    $obTxtValorComprometido = new Moeda;
    $obTxtValorComprometido->setName     ('nuValorComprometido');
    $obTxtValorComprometido->setId       ('nuValorComprometido');
    $obTxtValorComprometido->setRotulo   ('Valor Comprometido');
    $obTxtValorComprometido->setTitle    ('Informe o valor comprometido.');
    $obTxtValorComprometido->setSize     (14);
    $obTxtValorComprometido->setMaxLength(14);

    $obTxtValorTotal = new Moeda;
    $obTxtValorTotal->setName     ('nuValorTotal');
    $obTxtValorTotal->setId       ('nuValorTotal');
    $obTxtValorTotal->setRotulo   ('Valor Total');
    $obTxtValorTotal->setTitle    ('Informe o valor total.');
    $obTxtValorTotal->setSize     (14);
    $obTxtValorTotal->setMaxLength(14);

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumero);
    $obFormulario->addComponente($obDataEmissao);
    $obFormulario->addComponente($obDataSaida);
    $obFormulario->addComponente($obHoraSaida);
    $obFormulario->addComponente($obTxtDestino);
    $obFormulario->addComponente($obDataChegada);
    $obFormulario->addComponente($obHoraChegada);
    $obFormulario->addComponente($obTxtMotivo);
    $obFormulario->addComponente($obTxtValorComprometido);
    $obFormulario->addComponente($obTxtValorTotal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoDiaria()
{
    $obTxtFuncionario = new TextBox;
    $obTxtFuncionario->setName     ('stFuncionario');
    $obTxtFuncionario->setId       ('stFuncionario');
    $obTxtFuncionario->setValue    ('');
    $obTxtFuncionario->setRotulo   ('Funcionário');
    $obTxtFuncionario->setTitle    ('Informe o Funcionário');
    $obTxtFuncionario->setMaxLength(30);
    $obTxtFuncionario->setSize     (30);
    $obTxtFuncionario->setNull     (false);

    $obTxtMatricula = new TextBox;
    $obTxtMatricula->setName     ('stMatricula');
    $obTxtMatricula->setId       ('stMatricula');
    $obTxtMatricula->setValue    ('');
    $obTxtMatricula->setRotulo   ('Matrícula');
    $obTxtMatricula->setTitle    ('Informe o matrícula');
    $obTxtMatricula->setInteiro  (true);
    $obTxtMatricula->setMaxLength(10);
    $obTxtMatricula->setSize     (10);

    $obDataSaida = new Data;
    $obDataSaida->setName  ('dtSaida');
    $obDataSaida->setId    ('dtSaida');
    $obDataSaida->setRotulo('Informe a Data de Saída');

    $obHoraSaida = new Hora;
    $obHoraSaida->setName  ('hrSaida');
    $obHoraSaida->setId    ('hrSaida');
    $obHoraSaida->setRotulo('Informe a Hora de Saída');

    $obTxtDestino = new TextBox;
    $obTxtDestino->setName   ('stDestino');
    $obTxtDestino->setId     ('stDestino');
    $obTxtDestino->setValue  ('');
    $obTxtDestino->setRotulo ('Destino');
    $obTxtDestino->setTitle  ('Informe o Destino');
    $obTxtDestino->setMaxLength(25);
    $obTxtDestino->setSize     (25);

    $obDataRetorno = new Data;
    $obDataRetorno->setName  ('dtRetorno');
    $obDataRetorno->setId    ('dtRetorno');
    $obDataRetorno->setRotulo('Informe a Data de Retorno');

    $obHoraRetorno = new Hora;
    $obHoraRetorno->setName  ('hrRetorno');
    $obHoraRetorno->setId    ('hrRetorno');
    $obHoraRetorno->setRotulo('Informe a Hora de Retorno');

    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName         ('stMotivo');
    $obTxtMotivo->setId           ('stMotivo');
    $obTxtMotivo->setValue        ('');
    $obTxtMotivo->setRotulo       ('Motivo');
    $obTxtMotivo->setTitle        ('Informe o Motivo');
    $obTxtMotivo->setCols         (100            );
    $obTxtMotivo->setRows         (3              );
    $obTxtMotivo->setMaxCaracteres(120);

    $obTxtValorComprometido = new Moeda;
    $obTxtValorComprometido->setName     ('nuValorComprometido');
    $obTxtValorComprometido->setId       ('nuValorComprometido');
    $obTxtValorComprometido->setRotulo   ('Valor Comprometido');
    $obTxtValorComprometido->setTitle    ('Informe o valor comprometido.');
    $obTxtValorComprometido->setSize     (14);
    $obTxtValorComprometido->setMaxLength(14);

    $obTxtValorTotal = new Moeda;
    $obTxtValorTotal->setName     ('nuValorTotal');
    $obTxtValorTotal->setId       ('nuValorTotal');
    $obTxtValorTotal->setRotulo   ('Valor Total');
    $obTxtValorTotal->setTitle    ('Informe o valor total.');
    $obTxtValorTotal->setSize     (14);
    $obTxtValorTotal->setMaxLength(14);

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtFuncionario);
    $obFormulario->addComponente($obTxtMatricula);
    $obFormulario->addComponente($obDataSaida);
    $obFormulario->addComponente($obHoraSaida);
    $obFormulario->addComponente($obTxtDestino);
    $obFormulario->addComponente($obDataRetorno);
    $obFormulario->addComponente($obHoraRetorno);
    $obFormulario->addComponente($obTxtMotivo);
    $obFormulario->addComponente($obTxtValorComprometido);
    $obFormulario->addComponente($obTxtValorTotal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoDiverso()
{
    $obTxtNumero = new TextBox;
    $obTxtNumero->setName     ('stNumero');
    $obTxtNumero->setId       ('stNumero');
    $obTxtNumero->setValue    ('');
    $obTxtNumero->setRotulo   ('Número');
    $obTxtNumero->setTitle    ('Informe o Número');
    $obTxtNumero->setInteiro  (true);
    $obTxtNumero->setMaxLength(10);
    $obTxtNumero->setSize     (10);

    $obData = new Data;
    $obData->setName  ('dtDiverso');
    $obData->setId    ('dtDiverso');
    $obData->setRotulo('Informe a Data');

    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         ('stDescricao');
    $obTxtDescricao->setId           ('stDescricao');
    $obTxtDescricao->setValue        ('');
    $obTxtDescricao->setRotulo       ('Descrição');
    $obTxtDescricao->setTitle        ('Informe a Descrição');
    $obTxtDescricao->setCols         (100);
    $obTxtDescricao->setRows         (3);
    $obTxtDescricao->setMaxCaracteres(120);

    $obTxtNomeDocumento = new TextArea;
    $obTxtNomeDocumento->setName         ('stNomeDocumento');
    $obTxtNomeDocumento->setId           ('stNomeDocumento');
    $obTxtNomeDocumento->setValue        ('');
    $obTxtNomeDocumento->setRotulo       ('Nome Documento');
    $obTxtNomeDocumento->setTitle        ('Informe o Nome do Documento');
    $obTxtNomeDocumento->setCols         (100);
    $obTxtNomeDocumento->setRows         (3);
    $obTxtNomeDocumento->setMaxCaracteres(120);

    $obTxtValorComprometido = new Moeda;
    $obTxtValorComprometido->setName     ('nuValorComprometido');
    $obTxtValorComprometido->setId       ('nuValorComprometido');
    $obTxtValorComprometido->setRotulo   ('Valor Comprometido');
    $obTxtValorComprometido->setTitle    ('Informe o valor comprometido.');
    $obTxtValorComprometido->setSize     (14);
    $obTxtValorComprometido->setMaxLength(14);

    $obTxtValorTotal = new Moeda;
    $obTxtValorTotal->setName     ('nuValorTotal');
    $obTxtValorTotal->setId       ('nuValorTotal');
    $obTxtValorTotal->setRotulo   ('Valor Total');
    $obTxtValorTotal->setTitle    ('Informe o valor total.');
    $obTxtValorTotal->setSize     (14);
    $obTxtValorTotal->setMaxLength(14);

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumero);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtDescricao);
    $obFormulario->addComponente($obTxtNomeDocumento);
    $obFormulario->addComponente($obTxtValorComprometido);
    $obFormulario->addComponente($obTxtValorTotal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoFolha()
{
    $obExercicio = new Exercicio();
    $obExercicio->setId('stExercicio');

    $obMes = new Mes();
    $obMes->setId('inMes');

    $obTxtValorComprometido = new Moeda;
    $obTxtValorComprometido->setName     ('nuValorComprometido');
    $obTxtValorComprometido->setId       ('nuValorComprometido');
    $obTxtValorComprometido->setRotulo   ('Valor Comprometido');
    $obTxtValorComprometido->setTitle    ('Informe o valor comprometido.');
    $obTxtValorComprometido->setSize     (14);
    $obTxtValorComprometido->setMaxLength(14);

    $obTxtValorTotal = new Moeda;
    $obTxtValorTotal->setName     ('nuValorTotal');
    $obTxtValorTotal->setId       ('nuValorTotal');
    $obTxtValorTotal->setRotulo   ('Valor Total');
    $obTxtValorTotal->setTitle    ('Informe o valor total.');
    $obTxtValorTotal->setSize     (14);
    $obTxtValorTotal->setMaxLength(14);

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obExercicio);
    $obFormulario->addComponente($obMes);
    $obFormulario->addComponente($obTxtValorComprometido);
    $obFormulario->addComponente($obTxtValorTotal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoNota()
{
    $obTxtNumeroNotaFiscal = new TextBox;
    $obTxtNumeroNotaFiscal->setName     ('stNumeroNotaFiscal');
    $obTxtNumeroNotaFiscal->setId       ('stNumeroNotaFiscal');
    $obTxtNumeroNotaFiscal->setValue    ('');
    $obTxtNumeroNotaFiscal->setRotulo   ('Número Nota Fiscal');
    $obTxtNumeroNotaFiscal->setTitle    ('Informe o Número da Nota Fiscal');
    $obTxtNumeroNotaFiscal->setInteiro  (true);
    $obTxtNumeroNotaFiscal->setMaxLength(10);
    $obTxtNumeroNotaFiscal->setSize     (10);

    $obTxtNumeroSerie = new TextBox;
    $obTxtNumeroSerie->setName     ('stNumeroSerie');
    $obTxtNumeroSerie->setId       ('stNumeroSerie');
    $obTxtNumeroSerie->setValue    ('');
    $obTxtNumeroSerie->setRotulo   ('Número Série');
    $obTxtNumeroSerie->setTitle    ('Informe o Número da Série');
    $obTxtNumeroSerie->setInteiro  (true);
    $obTxtNumeroSerie->setMaxLength(3);
    $obTxtNumeroSerie->setSize     (3);

    $obTxtNumeroSubserie = new TextBox;
    $obTxtNumeroSubserie->setName     ('stNumeroSubserie');
    $obTxtNumeroSubserie->setId       ('stNumeroSubserie');
    $obTxtNumeroSubserie->setValue    ('');
    $obTxtNumeroSubserie->setRotulo   ('Número Subsérie');
    $obTxtNumeroSubserie->setTitle    ('Informe o Número da Subsérie');
    $obTxtNumeroSubserie->setInteiro  (true);
    $obTxtNumeroSubserie->setMaxLength(3);
    $obTxtNumeroSubserie->setSize     (3);

    $obData = new Data;
    $obData->setName  ('dtNota');
    $obData->setId    ('dtNota');
    $obData->setRotulo('Informe a Data');

    $obTxtValorComprometido = new Moeda;
    $obTxtValorComprometido->setName     ('nuValorComprometido');
    $obTxtValorComprometido->setId       ('nuValorComprometido');
    $obTxtValorComprometido->setRotulo   ('Valor Comprometido');
    $obTxtValorComprometido->setTitle    ('Informe o valor comprometido.');
    $obTxtValorComprometido->setSize     (14);
    $obTxtValorComprometido->setMaxLength(14);

    $obTxtValorTotal = new Moeda;
    $obTxtValorTotal->setName     ('nuValorTotal');
    $obTxtValorTotal->setId       ('nuValorTotal');
    $obTxtValorTotal->setRotulo   ('Valor Total');
    $obTxtValorTotal->setTitle    ('Informe o valor total.');
    $obTxtValorTotal->setSize     (14);
    $obTxtValorTotal->setMaxLength(14);

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroNotaFiscal);
    $obFormulario->addComponente($obTxtNumeroSerie);
    $obFormulario->addComponente($obTxtNumeroSubserie);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtValorComprometido);
    $obFormulario->addComponente($obTxtValorTotal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoRecibo()
{
    include_once CAM_GPC_TCEAM_NEGOCIO.'RTCEAMTipoRecibo.class.php';
    $obRTCEAMTipoRecibo = new RTCEAMTipoRecibo;
    $obRTCEAMTipoRecibo->recuperaTipoRecibo($rsTipoRecibo);

    include_once CAM_GPC_TCEAM_NEGOCIO.'RTCEAMTipoDocumentoRecibo.class.php';
    $obRTCEAMTipoDocumentoRecibo = new RTCEAMTipoDocumentoRecibo;
    $obRTCEAMTipoDocumentoRecibo->recuperaProximoNumeroRecibo($rsProximoNumeroRecibo);
    $stNumero = $rsProximoNumeroRecibo->getCampo('numeracao');

    $obTxtTipoRecibo = new TextBox;
    $obTxtTipoRecibo->setRotulo   ('Tipo Recibo');
    $obTxtTipoRecibo->setTitle    ('Informe o Tipo do Recibo');
    $obTxtTipoRecibo->setName     ('inCodTipoReciboTxt');
    $obTxtTipoRecibo->setValue    ('');
    $obTxtTipoRecibo->setSize     (4);
    $obTxtTipoRecibo->setMaxLength(3);
    $obTxtTipoRecibo->setInteiro  (true);
    $obTxtTipoRecibo->setNull     (false);

    $obCmbTipoRecibo = new Select;
    $obCmbTipoRecibo->setName      ('inCodTipoRecibo');
    $obCmbTipoRecibo->setId        ('inCodTipoRecibo');
    $obCmbTipoRecibo->addOption    ('', 'Selecione'  );
    $obCmbTipoRecibo->setCampoId   ('cod_tipo_recibo');
    $obCmbTipoRecibo->setCampoDesc ('descricao'      );
    $obCmbTipoRecibo->preencheCombo($rsTipoRecibo    );
    $obCmbTipoRecibo->setNull      (false            );

    $obLblNumero = new Label;
    $obLblNumero->setName     ('lblNumero');
    $obLblNumero->setId       ('lblNumero');
    $obLblNumero->setValue    ($stNumero);
    $obLblNumero->setRotulo   ('Número');

    $obHdnNumero = new Hidden;
    $obHdnNumero->setName ('stNumero');
    $obHdnNumero->setId   ('stNumero');
    $obHdnNumero->setValue($stNumero);

    $obTxtValor = new Moeda;
    $obTxtValor->setName     ('nuValor');
    $obTxtValor->setId       ('nuValor');
    $obTxtValor->setRotulo   ('Valor');
    $obTxtValor->setTitle    ('Informe o Valor.');
    $obTxtValor->setSize     (14);
    $obTxtValor->setMaxLength(14);

    $obData = new Data;
    $obData->setName  ('dtRecibo');
    $obData->setId    ('dtRecibo');
    $obData->setRotulo('Informe a Data');

    $obTxtValorComprometido = new Moeda;
    $obTxtValorComprometido->setName     ('nuValorComprometido');
    $obTxtValorComprometido->setId       ('nuValorComprometido');
    $obTxtValorComprometido->setRotulo   ('Valor Comprometido');
    $obTxtValorComprometido->setTitle    ('Informe o Valor Comprometido.');
    $obTxtValorComprometido->setSize     (14);
    $obTxtValorComprometido->setMaxLength(14);

    $obTxtValorTotal = new Moeda;
    $obTxtValorTotal->setName     ('nuValorTotal');
    $obTxtValorTotal->setId       ('nuValorTotal');
    $obTxtValorTotal->setRotulo   ('Valor Total');
    $obTxtValorTotal->setTitle    ('Informe o Valor Total.');
    $obTxtValorTotal->setSize     (14);
    $obTxtValorTotal->setMaxLength(14);

    $obFormulario = new Formulario;
    $obFormulario->addComponenteComposto($obTxtTipoRecibo, $obCmbTipoRecibo);
    $obFormulario->addComponente($obLblNumero);
    $obFormulario->addHidden($obHdnNumero);
    $obFormulario->addComponente($obTxtValor);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtValorComprometido);
    $obFormulario->addComponente($obTxtValorTotal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}


function montaCamposTipoDocumentoNotaFiscal()
{
    $obTxtNumeroNotaFiscal = new TextBox;
    $obTxtNumeroNotaFiscal->setName     ('inNumeroDocumento');
    $obTxtNumeroNotaFiscal->setId       ('inNumeroDocumento');
    $obTxtNumeroNotaFiscal->setValue    ('');
    $obTxtNumeroNotaFiscal->setRotulo   ('Número Nota Fiscal');
    $obTxtNumeroNotaFiscal->setTitle    ('Informe o Número da Nota Fiscal');
    $obTxtNumeroNotaFiscal->setInteiro  (true);
    $obTxtNumeroNotaFiscal->setNull     (false);
    if (Sessao::read('tipoEstado') == 'PE') {
        $obTxtNumeroNotaFiscal->setMaxLength(20);
        $obTxtNumeroNotaFiscal->setSize     (20);
    } else {
        $obTxtNumeroNotaFiscal->setMaxLength(15);
        $obTxtNumeroNotaFiscal->setSize     (15);
    }

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         ('stDescricao');
    $obTxtDescricao->setId           ('stDescricao');
    $obTxtDescricao->setValue        ('');
    $obTxtDescricao->setRotulo       ('Descrição');
    $obTxtDescricao->setTitle        ('Informe a Descrição');
    $obTxtDescricao->setCols         (100);
    $obTxtDescricao->setRows         (3);
    $obTxtDescricao->setMaxCaracteres(255);

    $obTxtAutorizacaoNotaFiscal = new TextBox;
    $obTxtAutorizacaoNotaFiscal->setName     ('stAutorizacao');
    $obTxtAutorizacaoNotaFiscal->setId       ('stAutorizacao');
    $obTxtAutorizacaoNotaFiscal->setValue    ('');
    $obTxtAutorizacaoNotaFiscal->setRotulo   ('*Autorização Nota Fiscal');
    $obTxtAutorizacaoNotaFiscal->setTitle    ('Informe a autorização da Nota Fiscal');
    $obTxtAutorizacaoNotaFiscal->setMaxLength(15);
    $obTxtAutorizacaoNotaFiscal->setSize     (15);

    $obTxtModeloNotaFiscal = new TextBox;
    $obTxtModeloNotaFiscal->setName     ('stModelo');
    $obTxtModeloNotaFiscal->setId       ('stModelo');
    $obTxtModeloNotaFiscal->setValue    ('');
    $obTxtModeloNotaFiscal->setRotulo   ('*Modelo Nota Fiscal');
    $obTxtModeloNotaFiscal->setTitle    ('Informe o Modelo da Nota Fiscal');
    $obTxtModeloNotaFiscal->setMaxLength(15);
    $obTxtModeloNotaFiscal->setSize     (15);
    
    if (Sessao::read('tipoEstado') == 'PE'){
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
        
        $obTMapeamento = new TUF;
        $stFiltro = " WHERE cod_pais = 1 ";
        $obTMapeamento->recuperaTodos($rsRecordSet, $stFiltro);

        $rsRecordSet->ordena("nom_uf",ASC,SORT_STRING);
        $obSelectUF = new Select();
        $obSelectUF->setRotulo          ( "UF do Documento"                                 );
        $obSelectUF->setTitle           ( "Selecione a Unidade de Federação do Documento."  );
        $obSelectUF->setName            ( "stUfDocumento"                                   );
        $obSelectUF->setId              ( "stUfDocumento"                                   );
        $obSelectUF->setNull            ( true                                              );
        $obSelectUF->addOption          ( "","Selecione"                                    );
        $obSelectUF->setCampoID         ( "cod_uf"                                          );
        $obSelectUF->setCampoDesc       ( "nom_uf"                                          );
        $obSelectUF->preencheCombo      ( $rsRecordSet                                      );        
        
        $inSerieDocumento = new Inteiro;
        $inSerieDocumento->setName      ('inSerieDocumento');
        $inSerieDocumento->setId        ('inSerieDocumento');
        $inSerieDocumento->setValue     ('');
        $inSerieDocumento->setRotulo    ('Série do Documento');
        $inSerieDocumento->setTitle     ('Informe a Série do Documento');
        $inSerieDocumento->setMaxLength (5);
        $inSerieDocumento->setSize      (5);
    }
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroNotaFiscal);
    if (Sessao::read('tipoEstado') != 'PE') {
        $obFormulario->addComponente($obData);
        $obFormulario->addComponente($obTxtDescricao);
        $obFormulario->addComponente($obTxtAutorizacaoNotaFiscal);
        $obFormulario->addComponente($obTxtModeloNotaFiscal);
    }
    if (Sessao::read('tipoEstado') == 'PE') { $obFormulario->addComponente($inSerieDocumento); }
    if (Sessao::read('tipoEstado') == 'PE') { $obFormulario->addComponente($obSelectUF); }
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoReciboAl()
{
    $obTxtNumeroReciboAl = new TextBox;
    $obTxtNumeroReciboAl->setName     ('inNumeroDocumento');
    $obTxtNumeroReciboAl->setId       ('inNumeroDocumento');
    $obTxtNumeroReciboAl->setValue    ('');
    $obTxtNumeroReciboAl->setRotulo   ('Número do Recibo');
    $obTxtNumeroReciboAl->setTitle    ('Informe o Número Recibo');
    $obTxtNumeroReciboAl->setInteiro  (true);
    $obTxtNumeroReciboAl->setMaxLength(15);
    $obTxtNumeroReciboAl->setSize     (15);
    $obTxtNumeroReciboAl->setNull     (false);

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricaoRecibo = new TextArea;
    $obTxtDescricaoRecibo->setName         ('stDescricao');
    $obTxtDescricaoRecibo->setId           ('stDescricao');
    $obTxtDescricaoRecibo->setValue        ('');
    $obTxtDescricaoRecibo->setRotulo       ('Descrição');
    $obTxtDescricaoRecibo->setTitle        ('Informe a Descrição');
    $obTxtDescricaoRecibo->setCols         (100);
    $obTxtDescricaoRecibo->setRows         (3);
    $obTxtDescricaoRecibo->setMaxCaracteres(255);
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroReciboAl);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtDescricaoRecibo);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoDiariaAl()
{
    $obTxtNumeroDiariaAl = new TextBox;
    $obTxtNumeroDiariaAl->setName     ('inNumeroDocumento');
    $obTxtNumeroDiariaAl->setId       ('inNumeroDocumento');
    $obTxtNumeroDiariaAl->setValue    ('');
    $obTxtNumeroDiariaAl->setRotulo   ('Número do Documento');
    $obTxtNumeroDiariaAl->setTitle    ('Informe o Número da Diária');
    $obTxtNumeroDiariaAl->setInteiro  (true);
    $obTxtNumeroDiariaAl->setMaxLength(15);
    $obTxtNumeroDiariaAl->setSize     (15);
    $obTxtNumeroDiariaAl->setNull     (false);

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricaoDiaria = new TextArea;
    $obTxtDescricaoDiaria->setName         ('stDescricao');
    $obTxtDescricaoDiaria->setId           ('stDescricao');
    $obTxtDescricaoDiaria->setValue        ('');
    $obTxtDescricaoDiaria->setRotulo       ('Descrição');
    $obTxtDescricaoDiaria->setTitle        ('Informe a Descrição');
    $obTxtDescricaoDiaria->setCols         (100);
    $obTxtDescricaoDiaria->setRows         (3);
    $obTxtDescricaoDiaria->setMaxCaracteres(255);
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroDiariaAl);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtDescricaoDiaria);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoFolhaPagamentoAl()
{
    $obTxtNumeroFolhaPagamento = new TextBox;
    $obTxtNumeroFolhaPagamento->setName     ('inNumeroDocumento');
    $obTxtNumeroFolhaPagamento->setId       ('inNumeroDocumento');
    $obTxtNumeroFolhaPagamento->setValue    ('');
    $obTxtNumeroFolhaPagamento->setRotulo   ('Número da Folha de Pagamento');
    $obTxtNumeroFolhaPagamento->setTitle    ('Informe o Número da Folha de Pagamento');
    $obTxtNumeroFolhaPagamento->setInteiro  (true);
    $obTxtNumeroFolhaPagamento->setMaxLength(15);
    $obTxtNumeroFolhaPagamento->setSize     (15);
    $obTxtNumeroFolhaPagamento->setNull     (false);

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricaoFolhaPagamento = new TextArea;
    $obTxtDescricaoFolhaPagamento->setName         ('stDescricao');
    $obTxtDescricaoFolhaPagamento->setId           ('stDescricao');
    $obTxtDescricaoFolhaPagamento->setValue        ('');
    $obTxtDescricaoFolhaPagamento->setRotulo       ('Descrição');
    $obTxtDescricaoFolhaPagamento->setTitle        ('Informe a Descrição');
    $obTxtDescricaoFolhaPagamento->setCols         (100);
    $obTxtDescricaoFolhaPagamento->setRows         (3);
    $obTxtDescricaoFolhaPagamento->setMaxCaracteres(255);
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroFolhaPagamento);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtDescricaoFolhaPagamento);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoBilheteAl()
{
    $obTxtNumeroBilheteAl = new TextBox;
    $obTxtNumeroBilheteAl->setName     ('inNumeroDocumento');
    $obTxtNumeroBilheteAl->setId       ('inNumeroDocumento');
    $obTxtNumeroBilheteAl->setValue    ('');
    $obTxtNumeroBilheteAl->setRotulo   ('Número do Bilhete');
    $obTxtNumeroBilheteAl->setTitle    ('Informe o Número do Bilhete');
    $obTxtNumeroBilheteAl->setInteiro  (true);
    $obTxtNumeroBilheteAl->setMaxLength(15);
    $obTxtNumeroBilheteAl->setSize     (15);
    $obTxtNumeroBilheteAl->setNull     (false);

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricaoBilhete = new TextArea;
    $obTxtDescricaoBilhete->setName         ('stDescricao');
    $obTxtDescricaoBilhete->setId           ('stDescricao');
    $obTxtDescricaoBilhete->setValue        ('');
    $obTxtDescricaoBilhete->setRotulo       ('Descrição');
    $obTxtDescricaoBilhete->setTitle        ('Informe a Descrição');
    $obTxtDescricaoBilhete->setCols         (100);
    $obTxtDescricaoBilhete->setRows         (3);
    $obTxtDescricaoBilhete->setMaxCaracteres(255);
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroBilheteAl);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtDescricaoBilhete);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoNFE()
{
    $obTxtNumeroNotaFiscal = new TextBox;
    $obTxtNumeroNotaFiscal->setName     ('inNumeroDocumento');
    $obTxtNumeroNotaFiscal->setId       ('inNumeroDocumento');
    $obTxtNumeroNotaFiscal->setValue    ('');
    $obTxtNumeroNotaFiscal->setRotulo   ('Número Nota Fiscal Eletrônica');
    $obTxtNumeroNotaFiscal->setTitle    ('Informe o Número da Nota Fiscal Eletrônica');
    $obTxtNumeroNotaFiscal->setInteiro  (true);
    $obTxtNumeroNotaFiscal->setMaxLength(15);
    $obTxtNumeroNotaFiscal->setSize     (15);
    $obTxtNumeroNotaFiscal->setNull     (false);

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         ('stDescricao');
    $obTxtDescricao->setId           ('stDescricao');
    $obTxtDescricao->setValue        ('');
    $obTxtDescricao->setRotulo       ('Descrição');
    $obTxtDescricao->setTitle        ('Informe a Descrição');
    $obTxtDescricao->setCols         (100);
    $obTxtDescricao->setRows         (3);
    $obTxtDescricao->setMaxCaracteres(255);

    $obTxtAutorizacaoNotaFiscal = new TextBox;
    $obTxtAutorizacaoNotaFiscal->setName     ('stAutorizacao');
    $obTxtAutorizacaoNotaFiscal->setId       ('stAutorizacao');
    $obTxtAutorizacaoNotaFiscal->setValue    ('');
    $obTxtAutorizacaoNotaFiscal->setRotulo   ('*Autorização Nota Fiscal Eletrônica');
    $obTxtAutorizacaoNotaFiscal->setTitle    ('Informe a autorização da Nota Fiscal Eletrônica');
    $obTxtAutorizacaoNotaFiscal->setMaxLength(15);
    $obTxtAutorizacaoNotaFiscal->setSize     (15);

    $obTxtModeloNotaFiscal = new TextBox;
    $obTxtModeloNotaFiscal->setName     ('stModelo');
    $obTxtModeloNotaFiscal->setId       ('stModelo');
    $obTxtModeloNotaFiscal->setValue    ('');
    $obTxtModeloNotaFiscal->setRotulo   ('*Modelo Nota Fiscal');
    $obTxtModeloNotaFiscal->setTitle    ('Informe o Modelo da Nota Fiscal Eletrônica');
    $obTxtModeloNotaFiscal->setMaxLength(15);
    $obTxtModeloNotaFiscal->setSize     (15);
    
    $obTxtNumXmlNFe = new TextBox;
    $obTxtNumXmlNFe->setName     ('stNumXmlNFe');
    $obTxtNumXmlNFe->setId       ('stNumXmlNFe');
    $obTxtNumXmlNFe->setValue    ('');
    $obTxtNumXmlNFe->setRotulo   ('*Número da Chave de Acesso');
    $obTxtNumXmlNFe->setTitle    ('Informe o Número da Chave de Acesso da NFe');
    $obTxtNumXmlNFe->setMaxLength(44);
    $obTxtNumXmlNFe->setSize     (44);
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroNotaFiscal);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtDescricao);
    $obFormulario->addComponente($obTxtAutorizacaoNotaFiscal);
    $obFormulario->addComponente($obTxtModeloNotaFiscal);
    $obFormulario->addComponente($obTxtNumXmlNFe);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoCupomFiscal()
{
    $obTxtNumeroCupomFiscal = new TextBox;
    $obTxtNumeroCupomFiscal->setName     ('inNumeroDocumento');
    $obTxtNumeroCupomFiscal->setId       ('inNumeroDocumento');
    $obTxtNumeroCupomFiscal->setValue    ('');
    $obTxtNumeroCupomFiscal->setRotulo   ('Número do Cupom Fiscal');
    $obTxtNumeroCupomFiscal->setTitle    ('Informe o Número do Cupom Fiscal');
    $obTxtNumeroCupomFiscal->setInteiro  (true);
    $obTxtNumeroCupomFiscal->setMaxLength(15);
    $obTxtNumeroCupomFiscal->setSize     (15);
    $obTxtNumeroCupomFiscal->setNull     (false);

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         ('stDescricao');
    $obTxtDescricao->setId           ('stDescricao');
    $obTxtDescricao->setValue        ('');
    $obTxtDescricao->setRotulo       ('Descrição');
    $obTxtDescricao->setTitle        ('Informe a Descrição');
    $obTxtDescricao->setCols         (100);
    $obTxtDescricao->setRows         (3);
    $obTxtDescricao->setMaxCaracteres(255);

    $obTxtAutorizacaoCupomFiscal = new TextBox;
    $obTxtAutorizacaoCupomFiscal->setName     ('stAutorizacao');
    $obTxtAutorizacaoCupomFiscal->setId       ('stAutorizacao');
    $obTxtAutorizacaoCupomFiscal->setValue    ('');
    $obTxtAutorizacaoCupomFiscal->setRotulo   ('Autorizacão Cupom Fiscal');
    $obTxtAutorizacaoCupomFiscal->setTitle    ('Informe a autorização do Cupom Fiscal');
    $obTxtAutorizacaoCupomFiscal->setMaxLength(15);
    $obTxtAutorizacaoCupomFiscal->setSize     (15);

    $obTxtModeloCupomFiscal = new TextBox;
    $obTxtModeloCupomFiscal->setName     ('stModelo');
    $obTxtModeloCupomFiscal->setId       ('stModelo');
    $obTxtModeloCupomFiscal->setValue    ('');
    $obTxtModeloCupomFiscal->setRotulo   ('Modelo Cupom Fiscal');
    $obTxtModeloCupomFiscal->setTitle    ('Informe o Modelo do Cupom Fiscal');
    $obTxtModeloCupomFiscal->setMaxLength(15);
    $obTxtModeloCupomFiscal->setSize     (15);
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroCupomFiscal);
    $obFormulario->addComponente($obData);
    $obFormulario->addComponente($obTxtDescricao);
    $obFormulario->addComponente($obTxtAutorizacaoCupomFiscal);
    $obFormulario->addComponente($obTxtModeloCupomFiscal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaCamposTipoDocumentoOutros() //NOW
{
    $obTxtNumeroOutros = new TextBox;
    $obTxtNumeroOutros->setName     ('inNumeroDocumento');
    $obTxtNumeroOutros->setId       ('inNumeroDocumento');
    $obTxtNumeroOutros->setValue    ('');
    $obTxtNumeroOutros->setRotulo   ('Número do Documento');
    $obTxtNumeroOutros->setTitle    ('Informe o Número do Documento');
    $obTxtNumeroOutros->setInteiro  (true);
    $obTxtNumeroOutros->setNull     (false);
    if (Sessao::read('tipoEstado') == 'PE') {
        $obTxtNumeroOutros->setMaxLength(20);
        $obTxtNumeroOutros->setSize     (20);
    } else {
        $obTxtNumeroOutros->setMaxLength(15);
        $obTxtNumeroOutros->setSize     (15);
    }

    $obData = new Data;
    $obData->setName  ('dtDocumento');
    $obData->setId    ('dtDocumento');
    $obData->setRotulo('Informe a Data');
    $obData->setNull  (false);
    
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         ('stDescricao');
    $obTxtDescricao->setId           ('stDescricao');
    $obTxtDescricao->setValue        ('');
    $obTxtDescricao->setRotulo       ('Descrição');
    $obTxtDescricao->setTitle        ('Informe a Descrição');
    $obTxtDescricao->setCols         (100);
    $obTxtDescricao->setRows         (3);
    $obTxtDescricao->setMaxCaracteres(255);

    $obTxtAutorizacaoNotaFiscalOutros = new TextBox;
    $obTxtAutorizacaoNotaFiscalOutros->setName     ('stAutorizacao');
    $obTxtAutorizacaoNotaFiscalOutros->setId       ('stAutorizacao');
    $obTxtAutorizacaoNotaFiscalOutros->setValue    ('');
    $obTxtAutorizacaoNotaFiscalOutros->setRotulo   ('Autorização Nota Fiscal');
    $obTxtAutorizacaoNotaFiscalOutros->setTitle    ('Informe a autorização da Nota Fiscal');
    $obTxtAutorizacaoNotaFiscalOutros->setMaxLength(15);
    $obTxtAutorizacaoNotaFiscalOutros->setSize     (15);

    $obTxtModeloOutros = new TextBox;
    $obTxtModeloOutros->setName     ('stModelo');
    $obTxtModeloOutros->setId       ('stModelo');
    $obTxtModeloOutros->setValue    ('');
    $obTxtModeloOutros->setRotulo   ('Modelo Nota Fiscal');
    $obTxtModeloOutros->setTitle    ('Informe o Modelo da Nota Fiscal ');
    $obTxtModeloOutros->setMaxLength(15);
    $obTxtModeloOutros->setSize     (15);
    
    if (Sessao::read('tipoEstado') == 'PE'){
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
        
        $obTMapeamento = new TUF;
        $stFiltro = " WHERE cod_pais = 1 ";
        $obTMapeamento->recuperaTodos($rsRecordSet, $stFiltro);

        $rsRecordSet->ordena("nom_uf",ASC,SORT_STRING);
        $obSelectUF = new Select();
        $obSelectUF->setRotulo          ( "UF do Documento"                                 );
        $obSelectUF->setTitle           ( "Selecione a Unidade de Federação do Documento."  );
        $obSelectUF->setName            ( "stUfDocumento"                                   );
        $obSelectUF->setId              ( "stUfDocumento"                                   );
        $obSelectUF->setNull            ( true                                              );
        $obSelectUF->addOption          ( "","Selecione"                                    );
        $obSelectUF->setCampoID         ( "cod_uf"                                          );
        $obSelectUF->setCampoDesc       ( "nom_uf"                                          );
        $obSelectUF->preencheCombo      ( $rsRecordSet                                      );
        
        $inSerieDocumento = new Inteiro;
        $inSerieDocumento->setName      ('inSerieDocumento');
        $inSerieDocumento->setId        ('inSerieDocumento');
        $inSerieDocumento->setValue     ('');
        $inSerieDocumento->setRotulo    ('Série do Documento');
        $inSerieDocumento->setTitle     ('Informe a Série do Documento');
        $inSerieDocumento->setMaxLength (5);
        $inSerieDocumento->setSize      (5);
    }
    
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obTxtNumeroOutros);
    if (Sessao::read('tipoEstado') != 'PE') {
        $obFormulario->addComponente($obData);
        $obFormulario->addComponente($obTxtDescricao);
        $obFormulario->addComponente($obTxtAutorizacaoNotaFiscalOutros);
        $obFormulario->addComponente($obTxtModeloOutros);
    }
    if (Sessao::read('tipoEstado') == 'PE') { $obFormulario->addComponente($inSerieDocumento); }
    if (Sessao::read('tipoEstado') == 'PE') { $obFormulario->addComponente($obSelectUF); }
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs = "d.getElementById('spnTipoDocumento').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaNF( $ent ){
    $stHtml = '';
    
    if($ent){
        include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoNotaFiscal.class.php";
        $obTTCEMGTipoNotaFiscal = new TTCEMGTipoNotaFiscal;
    
        $stOrder = " ORDER BY descricao ";
        $obTTCEMGTipoNotaFiscal->recuperaTodos($rsTipoNota, "", $stOrder);
        
        $obCmbTipoNota = new Select;
        $obCmbTipoNota->setName      ( "inCodTipoNota"             );
        $obCmbTipoNota->setRotulo    ( "Tipo Docto Fiscal"         );
        $obCmbTipoNota->setId        ( "stTipoDocto"               );
        $obCmbTipoNota->setCampoId   ( "cod_tipo"                  );
        $obCmbTipoNota->setCampoDesc ( "descricao"                 );
        $obCmbTipoNota->addOption    ( '','Selecione'              );
        $obCmbTipoNota->preencheCombo( $rsTipoNota                 );
        $obCmbTipoNota->setNull      ( false );
        $obCmbTipoNota->setValue     ( ''    );
        $obCmbTipoNota->obEvento->setOnChange("montaParametrosGET('montaTipoNF', 'stTipoDocto');");
        
        $obTxtIncricaoMunicipal = new TextBox;
        $obTxtIncricaoMunicipal->setName     ( "inNumInscricaoMunicipal"                   );
        $obTxtIncricaoMunicipal->setId       ( "inNumInscricaoMunicipal"                   );
        $obTxtIncricaoMunicipal->setRotulo   ( "Inscrição Municipal"                       );
        $obTxtIncricaoMunicipal->setTitle    ( "Informe o número da Inscrição Municipal da Entidade.");
        $obTxtIncricaoMunicipal->setNull     ( true                                        );
        $obTxtIncricaoMunicipal->setInteiro  ( true                                        );
        $obTxtIncricaoMunicipal->setSize     ( 25                                          );
        $obTxtIncricaoMunicipal->setMaxLength( 30                                          );
        
        $obTxtIncricaoEstadual = new TextBox;
        $obTxtIncricaoEstadual->setName     ( "inNumInscricaoEstadual"                   );
        $obTxtIncricaoEstadual->setId       ( "inNumInscricaoEstadual"                   );
        $obTxtIncricaoEstadual->setRotulo   ( "Inscrição Estadual"                       );
        $obTxtIncricaoEstadual->setTitle    ( "Informe o número da Inscrição Estadual da Entidade." );
        $obTxtIncricaoEstadual->setNull     ( true                                       );
        $obTxtIncricaoEstadual->setInteiro  ( true                                       );
        $obTxtIncricaoEstadual->setSize     ( 25                                         );
        $obTxtIncricaoEstadual->setMaxLength( 30                                         );
        
        $obTxtAIDF = new TextBox;
        $obTxtAIDF->setName     ( "stAIFD"                    );
        $obTxtAIDF->setId       ( "stAIDF"                    );
        $obTxtAIDF->setRotulo   ( "Número da AIDF"            );
        $obTxtAIDF->setTitle    ( "Informe o número da Autorização da Impressão do Documento Fiscal." );
        $obTxtAIDF->setNull     ( true                        );
        $obTxtAIDF->setInteiro  ( false                       );
        $obTxtAIDF->setSize     ( 18                          );
        $obTxtAIDF->setMaxLength( 15                          );
        
        $obDtEmissao = new Data;
        $obDtEmissao->setName     ( "dtEmissaoNF"                       );
        $obDtEmissao->setId       ( "dtEmissaoNF"                       );
        $obDtEmissao->setRotulo   ( "Data de Emissão"                   );
        $obDtEmissao->setValue    ( $_REQUEST['dtEmissao']              );
        $obDtEmissao->setTitle    ( 'Informe a data de emissão.'        );
        $obDtEmissao->setNull     ( false                               );
        $obDtEmissao->setSize     ( 10                                  );
        $obDtEmissao->setMaxLength( 10                                  );
        
        $obTxtExercicio = new TextBox;
        $obTxtExercicio->setName     ( "stExercicioNF"          );
        $obTxtExercicio->setId       ( "stExercicioNF"          );
        $obTxtExercicio->setValue    ( Sessao::getExercicio()   );
        $obTxtExercicio->setRotulo   ( "Exercício"              );
        $obTxtExercicio->setTitle    ( "Informe o exercício."   );
        $obTxtExercicio->setInteiro  ( false                    );
        $obTxtExercicio->setNull     ( false                    );
        $obTxtExercicio->setMaxLength( 4                        );
        $obTxtExercicio->setSize     ( 5                        );
        
        $obSpnTipoNF = new Span();
        $obSpnTipoNF->setId( 'spnTipoNF' );

        $obTxtVlTotalDoctoFiscal = new Label;
        $obTxtVlTotalDoctoFiscal->setName     ( "nuLbTotalNf"       );
        $obTxtVlTotalDoctoFiscal->setId       ( "nuLbTotalNf"       );
        $obTxtVlTotalDoctoFiscal->setRotulo   ( "Valor Total Docto Fiscal"  );
        $obTxtVlTotalDoctoFiscal->setNull     ( false                       );
        $obTxtVlTotalDoctoFiscal->setValue    ( '0,00'                      );
        
        $obTxtVlDescontoDoctoFiscal = new Numerico;
        $obTxtVlDescontoDoctoFiscal->setName     ( "nuVlDesconto"       );
        $obTxtVlDescontoDoctoFiscal->setId       ( "nuVlDesconto"       );
        $obTxtVlDescontoDoctoFiscal->setRotulo   ( "Valor Desconto Docto Fiscal");
        $obTxtVlDescontoDoctoFiscal->setAlign    ( 'RIGHT'                      );
        $obTxtVlDescontoDoctoFiscal->setMaxLength( 19                           );
        $obTxtVlDescontoDoctoFiscal->setSize     ( 21                           );
        $obTxtVlDescontoDoctoFiscal->setNull     ( false                        );
        $obTxtVlDescontoDoctoFiscal->setValue    ( '0,00'                       );
        $obTxtVlDescontoDoctoFiscal->obEvento->setOnChange("montaParametrosGET('atualizaValorLiquido','nuTotalNf, nuVlDesconto');" );
        
        $obTxtVlTotalLiquidNF = new Label;
        $obTxtVlTotalLiquidNF->setName    ( "nuTotalLiquidNf"               );
        $obTxtVlTotalLiquidNF->setId      ( "nuTotalLiquidNf"               );
        $obTxtVlTotalLiquidNF->setRotulo  ( "Valor Líquido Docto Fiscal"    );
        $obTxtVlTotalLiquidNF->setValue   ( '0,00'                          );
        $obTxtVlTotalLiquidNF->setNull    ( false                           );
        
        $obFormulario = new Formulario;

        switch (Sessao::read('inUf')){
            case '23':
                montaNumeroNF($obFormulario, true, "");
                montaNumSerie($obFormulario, true, "");
                $obFormulario->addComponente($obDtEmissao);
            break;
            default:
                $obFormulario->addComponente    ( $obCmbTipoNota                );
                $obFormulario->addComponente    ( $obTxtIncricaoMunicipal       );
                $obFormulario->addComponente    ( $obTxtIncricaoEstadual        );
                $obFormulario->addComponente    ( $obTxtAIDF                    );
                $obFormulario->addComponente    ( $obDtEmissao                  );
                $obFormulario->addComponente    ( $obTxtExercicio               );
                $obFormulario->addSpan          ( $obSpnTipoNF                  );
                $obFormulario->addTitulo        ( "Dados Financeiros do Documento Fiscal"   );
                $obFormulario->addComponente    ( $obTxtVlTotalDoctoFiscal      );
                $obFormulario->addComponente    ( $obTxtVlDescontoDoctoFiscal   );
                $obFormulario->addComponente    ( $obTxtVlTotalLiquidNF         );
            break;
        }

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        
    }
    
    $stJs = "d.getElementById('spnNF').innerHTML = '".$stHtml."';";
    
    if($ent)
        $stJs .= "montaParametrosGET('atualizaValorLiquido','nuTotalNf, nuVlDesconto');";
    
    echo $stJs;
}

//Se $boMunicipal = false Tipo de Chave Estadual, Se True = Tipo Municipal.
function montaChaveAcesso(&$obFormulario, $boMunicipal = false, $value = "")
{
    if ($boMunicipal == true) {
        $stNome = "Municipal";
        $Size = 60;
        $boMunicipal = false; //campo deve ser de preenchimento obrigatório
    } else {
        $stNome = "";
        $Size = 44;
    }
    
    $stTitulo = ($stNome == "Municipal") ? " ".$stNome."." : ".";
        
    $obTxtChave = new TextBox;
    $obTxtChave->setName      ( "inChave".$stNome                     );
    $obTxtChave->setId        ( "inChave".$stNome                     );
    $obTxtChave->setValue     ( $value                                );
    $obTxtChave->setRotulo    ( "Chave de Acesso ".$stNome            );
    $obTxtChave->setTitle     ( "Informe a Chave de Acesso".$stTitulo );
    $obTxtChave->setNull      ( $boMunicipal                          );
    $obTxtChave->setInteiro   ( false                                 );
    $obTxtChave->setSize      ( $Size                                 );
    $obTxtChave->setMaxLength ( $Size                                 );

    $obFormulario->addComponente( $obTxtChave );
}

function montaNumeroNF(&$obFormulario, $ent = true, $value = "")
{
    $obTxtNumNF = new TextBox;
    $obTxtNumNF->setName      ( "inNumeroNF"                         );
    $obTxtNumNF->setId        ( "inNumeroNF"                         );
    $obTxtNumNF->setValue     ( $value                               );
    $obTxtNumNF->setRotulo    ( "Número do Docto Fiscal"             );
    $obTxtNumNF->setTitle     ( "Informe o número do Docto Fiscal."  );
    $obTxtNumNF->setNull      ( $ent                                 );
    $obTxtNumNF->setInteiro   ( true                                 );
    $obTxtNumNF->setSize      ( 20                                   );
    $obTxtNumNF->setMaxLength ( 20                                   );

    $obFormulario->addComponente( $obTxtNumNF );
}

function montaNumSerie(&$obFormulario, $ent = true, $value = "")
{
    $obTxtNumSerie = new TextBox;
    $obTxtNumSerie->setName      ( "inNumSerie"                         );
    $obTxtNumSerie->setId        ( "inNumSerie"                         );
    $obTxtNumSerie->setValue     ( $value                               );
    $obTxtNumSerie->setRotulo    ( "Série do Docto Fiscal"              );
    $obTxtNumSerie->setTitle     ( "Informe a série do Docto Fiscal."   );
    $obTxtNumSerie->setNull      ( $ent                                 );
    $obTxtNumSerie->setInteiro   ( false                                );
    $obTxtNumSerie->setSize      ( 8                                    );
    $obTxtNumSerie->setMaxLength ( 8                                    );

    $obFormulario->addComponente( $obTxtNumSerie );
}

function montaTipoNF($boHabilita, $boNroNF, $boNroSerie, $boObrigatorio, $boChaveAcesso, $boMunicipal){
    $stHtml = "";
    
    if($boHabilita){
        $obFormulario = new Formulario;
        
        if ($boNroNF) {
            montaNumeroNF($obFormulario, $boObrigatorio);        
        }
        
        if ($boNroSerie) {
            montaNumSerie($obFormulario, $boObrigatorio);
        }
    
        if ($boChaveAcesso) {
            montaChaveAcesso($obFormulario, $boMunicipal);
        }
        
        $obFormulario->montaInnerHTML();
    
        $stHtml = $obFormulario->getHTML();
    }
    
    $stJs = "d.getElementById('spnTipoNF').innerHTML = '".$stHtml."';";
    
    echo $stJs;    
}

switch ($stCtrl) {

    case 'montaListaItemPreEmpenho':
        echo montaLista( Sessao::read('FiltroItens') );
    break;

    case 'totalizaItens':
        $count = 1;
        for ($inCount = 0; $inCount <  $_REQUEST['hidden'] ; $inCount++) {

           $total += str_replace(',','.',str_replace('.','',$_REQUEST[nuValor_.$count._.$count]));
           $count++;
        }

        $totalaLiq = number_format($total,2,',','.');

        $totalEmp = str_replace(',','.',str_replace('.','',$_REQUEST[vlTotalEmpenho]));

        $ValorTotalSaldo = $totalEmp - ($total + $_REQUEST[vlTotalLiquidado] - $_REQUEST[vlTotalLiquidadoAnulado]);

        $ValorTotalSaldo = number_format($ValorTotalSaldo,2,',','.');

        $js .= "d.getElementById('nuValorTotal').innerHTML = '".$totalaLiq."';
                d.getElementById('nuValorTotalSaldo').innerHTML = '".$ValorTotalSaldo."';
               ";
        if((SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()))==11){
            $js .= "d.getElementById('nuTotalNf').value = '".$totalaLiq."';
                    if(d.getElementById('stTipoDocto')){
                       montaParametrosGET('atualizaValorLiquido','nuTotalNf, nuVlDesconto');
                    } ";
        }
        echo $js;
    break;

    case "buscaHistorico":
    if ($_POST["inCodHistoricoPatrimon"] != "") {
        $obRContabilidadeHistoricoPadrao  = new RContabilidadeHistoricoPadrao;
        $obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST["inCodHistoricoPatrimon"] );
        $obRContabilidadeHistoricoPadrao->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeHistoricoPadrao->consultar();
        $stNomHistorico = $obRContabilidadeHistoricoPadrao->getNomHistorico();
        $boComplemento = ($obRContabilidadeHistoricoPadrao->getComplemento() == 't') ? true : false;
        if (!$stNomHistorico) {
            $js .= 'f.inCodHistoricoPatrimon.value = "";';
            $js .= 'f.inCodHistoricoPatrimon.focus();';
            $js .= 'd.getElementById("stNomHistoricoPatrimon").innerHTML = "&nbsp;";';
            $js .= "alertaAvisoTelaPrincipal('@Valor inválido. (".$_POST["inCodHistoricoPatrimon"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomHistoricoPatrimon").innerHTML = "'.$stNomHistorico.'";';
        }
        if ($boComplemento) {
            $js .= 'f.stComplemento.disabled=false;';
        } else {
            $js .= 'f.stComplemento.disabled=true;';
        }
    } else $js .= 'd.getElementById("stNomHistoricoPatrimon").innerHTML = "&nbsp;";';
        SistemaLegado::executaiFrameOculto($js);
    break;

    case "buscaContaContabilFinanc":
    if ($_POST['inCodContaContabilFinanc'] != "") {
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaContabilFinanc'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaDebito = $obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaDebito) {
            $js .= 'f.inCodContaContabilFinanc.value = "";';
            $js .= 'f.inCodContaContabilFinanc.focus();';
            $js .= 'd.getElementById("stNomContaContabilFinanc").innerHTML = "&nbsp;";';
            $js .= "alertaAvisoTelaPrincipal('@Valor inválido. (".$_POST["inCodContaContabilFinanc"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomContaContabilFinanc").innerHTML = "'.$stNomContaDebito.'";';
        }
    } else $js .= 'd.getElementById("stNomContaContabilFinanc").innerHTML = "&nbsp;";';
     SistemaLegado::executaiFrameOculto($js);
    break;

    case "buscaCDebPatrimon1":
        if ($_POST['inCodContaDebPatrimon'] != "") {
            $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaDebPatrimon'] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->setCodIniEstrutural("1.2.3");
            $obRContabilidadePlanoContaAnalitica->consultar();
            $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();
            if (!$stNomContaCredito) {
                $js .= 'f.inCodContaDebPatrimon.value = "";';
                $js .= 'f.inCodContaDebPatrimon.focus();';
                $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "&nbsp;";';
                $js .= "alertaAvisoTelaPrincipal('@Valor inválido. (".$_POST["inCodContaDebPatrimon"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "'.$stNomContaCredito.'";';
            }
        } else $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "&nbsp;";';
        SistemaLegado::executaiFrameOculto($js);
    break;

    case "buscaCCredPatrimon1":
        if ($_POST['inCodContaCredPatrimon'] != "") {
            $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaCredPatrimon'] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->setCodIniEstrutural("6.1.3.0");
            $obRContabilidadePlanoContaAnalitica->consultar();
            $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();

            if (!$stNomContaCredito) {
                $js .= 'f.inCodContaCredPatrimon.value = "";';
                $js .= 'f.inCodContaCredPatrimon.focus();';
                $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "&nbsp;";';
                $js .= "alertaAvisoTelaPrincipal('@Valor inválido. (".$_POST["inCodContaCredPatrimon"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "'.$stNomContaCredito.'";';
            }
        } else $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "&nbsp;";';
        SistemaLegado::executaiFrameOculto($js);
    break;

    case "buscaCDebPatrimon2":
        if ($_POST['inCodContaDebPatrimon'] != "") {
            $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaDebPatrimon'] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->setCodIniEstrutural("2");
            $obRContabilidadePlanoContaAnalitica->consultar();
            $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();

            if (!$stNomContaCredito) {
                $js .= 'f.inCodContaDebPatrimon.value = "";';
                $js .= 'f.inCodContaDebPatrimon.focus();';
                $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "&nbsp;";';
                $js .= "alertaAvisoTelaPrincipal('@Valor inválido. (".$_POST["inCodContaDebPatrimon"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "'.$stNomContaCredito.'";';
            }
        } else $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "&nbsp;";';
        SistemaLegado::executaiFrameOculto($js);
    break;

    case "buscaCCredPatrimon2":
        if ($_POST['inCodContaCredPatrimon'] != "") {
            $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
            $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaCredPatrimon'] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->setCodIniEstrutural("6.1.3.3");
            $obRContabilidadePlanoContaAnalitica->consultar();
            $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();

            if (!$stNomContaCredito) {
                $js .= 'f.inCodContaCredPatrimon.value = "";';
                $js .= 'f.inCodContaCredPatrimon.focus();';
                $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "&nbsp;";';
                $js .= "alertaAvisoTelaPrincipal('@Valor inválido. (".$_POST["inCodContaCredPatrimon"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "'.$stNomContaCredito.'";';
            }
        } else $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "&nbsp;";';
        SistemaLegado::executaiFrameOculto($js);
    break;

    case 'verificaDataLiquidacao':
    if ($_REQUEST['stDtLiquidacao'] != "" and $_REQUEST['inCodEntidade'] != "") {
        $obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
        $obREmpenhoNotaLiquidacao     = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho );
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setDtEmpenho($_REQUEST['stDtEmpenho']);
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho( $_REQUEST['inCodEmpenho'] );
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['dtExericioEmpenho']);
        $obREmpenhoNotaLiquidacao->setExercicio( Sessao::getExercicio() );
        $obREmpenhoNotaLiquidacao->listarMaiorData( $rsMaiorData );
        $obREmpenhoNotaLiquidacao->listarMaiorDataAnulacaoEmpenho($rsMaiorDataAnulacao);

        $stMaiorDataAnulacao = $rsMaiorDataAnulacao->getCampo('dataanulacao');

        if ($stMaiorDataAnulacao != '') {
            if (SistemaLegado::comparaDatas($stMaiorDataAnulacao, $_POST["stDtLiquidacao"])) {
                $js .= "f.stDtLiquidacao.value = '".$stMaiorDataAnulacao."';";
                $js .= 'window.parent.document.frm.stDtLiquidacao.focus();';
                $js .= "alertaAvisoTelaPrincipal('@Este empenho possui anulação de liquidação em ".$stMaiorDataAnulacao."','form','erro','".Sessao::getId()."');";
            } elseif (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "data_liquidacao" ) , $_POST["stDtLiquidacao"])) {
                $js .= "f.stDtLiquidacao.value = '".$rsMaiorData->getCampo( "data_liquidacao" )."';";
                $js .= 'window.parent.document.frm.stDtLiquidacao.focus();';
                $js .= "alertaAvisoTelaPrincipal('@Data de Liquidação deve ser maior ou igual a ".$rsMaiorData->getCampo( "data_liquidacao" )." !','form','erro','".Sessao::getId()."');";
            }
        } elseif (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "data_liquidacao" ),$_POST["stDtLiquidacao"])) {
            $js .= "f.stDtLiquidacao.value = '".$rsMaiorData->getCampo( "data_liquidacao" )."';";
            $js .= 'window.parent.document.frm.stDtLiquidacao.focus();';
            $js .= "alertaAvisoTelaPrincipal('@Data de Liquidação deve ser maior ou igual a ".$rsMaiorData->getCampo( "data_liquidacao" )." !','form','erro','".Sessao::getId()."');";
        }
    }
    SistemaLegado::executaiFrameOculto($js);
    break;
    case 'alteraCamposTipoDocumento':
        $stUf = Sessao::read('tipoEstado');
        $inCodTipoDocumento = $request->get('inCodTipoDocumento');
        switch ($stUf) {
            case 'AM':
                //if (Sessao::read('tipoEstado') == 'AM') {
                switch ($inCodTipoDocumento) {
                    case 1:
                        $js = montaCamposTipoDocumentoBilhete();
                    break;
                    case 2:
                        $js = montaCamposTipoDocumentoDiaria();
                    break;
                    case 3:
                        $js = montaCamposTipoDocumentoDiverso();
                    break;
                    case 4:
                        $js = montaCamposTipoDocumentoFolha();
                    break;
                    case 5:
                        $js = montaCamposTipoDocumentoNota();
                    break;
                    case 6:
                        $js = montaCamposTipoDocumentoRecibo();
                    break;
                    default:
                        $js = "d.getElementById('spnTipoDocumento').innerHTML = '';";
                    break;
                }
            break;
            /*
            if ($_REQUEST['inCodTipoDocumento'] == 1) {
                $js = montaCamposTipoDocumentoBilhete();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 2) {
                $js = montaCamposTipoDocumentoDiaria();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 3) {
                $js = montaCamposTipoDocumentoDiverso();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 4) {
                $js = montaCamposTipoDocumentoFolha();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 5) {
                $js = montaCamposTipoDocumentoNota();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 6) {
                $js = montaCamposTipoDocumentoRecibo();
            } else {
                $js = "d.getElementById('spnTipoDocumento').innerHTML = '';";
            }
            */
            case 'AL':
                //} elseif (Sessao::read('tipoEstado') == 'AL') {
                switch ($inCodTipoDocumento) {
                    case 1:
                        $js = montaCamposTipoDocumentoNotaFiscal();
                    break;
                    case 2:
                        $js = montaCamposTipoDocumentoReciboAl();
                    break;
                    case 3:
                        $js = montaCamposTipoDocumentoDiariaAl();
                    break;
                    case 4:
                        $js = montaCamposTipoDocumentoFolhaPagamentoAl();
                    break;
                    case 5:
                        $js = montaCamposTipoDocumentoBilheteAl();
                    break;
                    case 6:
                        $js = montaCamposTipoDocumentoNFE();
                    break;
                    case 7:
                        $js = montaCamposTipoDocumentoCupomFiscal();
                    break;
                    case 9:
                        $js = montaCamposTipoDocumentoOutros();
                    break;
                    default:
                        $js = "d.getElementById('spnTipoDocumento').innerHTML = '';";
                    break;
                }
            break;
            /*
            if ($_REQUEST['inCodTipoDocumento'] == 1) {
                $js = montaCamposTipoDocumentoNotaFiscal();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 2) {
                $js = montaCamposTipoDocumentoReciboAl();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 3) {
                $js = montaCamposTipoDocumentoDiariaAl();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 4) {
                $js = montaCamposTipoDocumentoFolhaPagamentoAl();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 5) {
                $js = montaCamposTipoDocumentoBilheteAl();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 6) {
                $js = montaCamposTipoDocumentoNFE();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 7) {
                $js = montaCamposTipoDocumentoCupomFiscal();
            } elseif ($_REQUEST['inCodTipoDocumento'] == 9) {
                $js = montaCamposTipoDocumentoOutros();
            } else {
                $js = "d.getElementById('spnTipoDocumento').innerHTML = '';";
            }
            */
            case 'PE':
                switch ($inCodTipoDocumento) {
                    case 1:
                        $js = montaCamposTipoDocumentoNotaFiscal();
                    break;
                    case 9:
                        $js = montaCamposTipoDocumentoOutros();
                    break;
                }
            break;
            case 'TO':
                switch ($inCodTipoDocumento) {
                    case 1:
                        $js = montaCamposTipoDocumentoNotaFiscal();
                    break;
                    case 2:
                        $js = montaCamposTipoDocumentoReciboAl();
                    break;
                    case 3:
                        $js = montaCamposTipoDocumentoDiariaAl();
                    break;
                    case 4:
                        $js = montaCamposTipoDocumentoFolhaPagamentoAl();
                    break;
                    case 5:
                        $js = montaCamposTipoDocumentoBilheteAl();
                    break;
                    case 9:
                        $js = montaCamposTipoDocumentoOutros();
                    break;
                }
            break;
                /*} else {
                if ($_REQUEST['inCodTipoDocumento'] == 1) {
                    $js = montaCamposTipoDocumentoNotaFiscal();
                } elseif ($_REQUEST['inCodTipoDocumento'] == 9) {
                    $js = montaCamposTipoDocumentoOutros();
                } else {
                    $js = "d.getElementById('spnTipoDocumento').innerHTML = '';";
                }
                */
        }
        
        echo $js;
        
    break;
/*
    case 'alteraCamposTipoDocumentoAL':

        if ($_REQUEST['inCodTipoDocumento'] == 1) {
            $js = montaCamposTipoDocumentoNotaFiscal();
        } elseif ($_REQUEST['inCodTipoDocumento'] == 2) {
            $js = montaCamposTipoDocumentoReciboAl();
        } elseif ($_REQUEST['inCodTipoDocumento'] == 3) {
            $js = montaCamposTipoDocumentoDiariaAl();
        } elseif ($_REQUEST['inCodTipoDocumento'] == 4) {
            $js = montaCamposTipoDocumentoFolhaPagamentoAl();
        } elseif ($_REQUEST['inCodTipoDocumento'] == 5) {
            $js = montaCamposTipoDocumentoBilheteAl();
        } elseif ($_REQUEST['inCodTipoDocumento'] == 6) {
            $js = montaCamposTipoDocumentoNFE();
        } elseif ($_REQUEST['inCodTipoDocumento'] == 7) {
            $js = montaCamposTipoDocumentoCupomFiscal();
        } elseif ($_REQUEST['inCodTipoDocumento'] == 9) {
            $js = montaCamposTipoDocumentoOutros();
        } else {
            $js = "d.getElementById('spnTipoDocumento').innerHTML = '';";
        }

        echo $js;
    break;
*/
    case "montaNF" :
        if($_REQUEST['stIncluirNF'] == "Não"){
            montaNF(false);  
        }
        else if($_REQUEST['stIncluirNF'] == "Sim"){
            montaNF(true);  
        }
    break;

    case "montaTipoNF" :
        $inCodTipoNota  = $_REQUEST['inCodTipoNota'];
        
        $boHabilita     = true;
        $boNroNF        = false;
        $boNroSerie     = false;
        $boObrigatorio  = true;
        $boChaveAcesso  = false;
        $boMunicipal    = false;
        
        if ($inCodTipoNota == 1 || $inCodTipoNota == 4) {
            $boChaveAcesso = true;

            if ($inCodTipoNota == 1) {
                $boNroNF = true;
                $boNroSerie = true;
            }
        }
        elseif ($inCodTipoNota == "") {
            $boHabilita = false;
        }
        else {
            $boNroNF = true;
            $boNroSerie = true;
            $boObrigatorio = false;
            
            if ($inCodTipoNota == 2) {
                $boChaveAcesso = true;
                $boMunicipal = true;
            }
        }
        
        montaTipoNF($boHabilita, $boNroNF, $boNroSerie, $boObrigatorio, $boChaveAcesso, $boMunicipal);
    break;

    case "atualizaValorLiquido":
        if ($_REQUEST['nuTotalNf'] != '' && $_REQUEST['nuVlDesconto'] != '') {
            $nuTotalNf = str_replace('.', '' , $_REQUEST['nuTotalNf']);
            $nuTotalNf = str_replace(',', '.', $nuTotalNf);
            $nuVlDesconto = str_replace('.', '' , $_REQUEST['nuVlDesconto']);
            $nuVlDesconto = str_replace(',', '.', $nuVlDesconto);

            $nuTotalLiquidNf = (float)$nuTotalNf - (float)$nuVlDesconto;

            $stJs = "jQuery('#nuTotalLiquidNf').val('".number_format($nuTotalLiquidNf,2,',','.')."');";
            $stJs .= "jQuery('#nuTotalLiquidNf').html('".number_format($nuTotalLiquidNf,2,',','.')."');";
            $stJs .= "jQuery('#nuLbTotalNf').html('".number_format($nuTotalNf,2,',','.')."');";
        } else {
            $stJs = "jQuery('#nuTotalLiquidNf').val('');";
        }

        echo $stJs;

    break;
}
?>
