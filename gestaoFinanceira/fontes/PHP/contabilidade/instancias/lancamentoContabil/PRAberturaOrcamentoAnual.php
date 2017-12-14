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
    * Abertura Orcamento Anual
    * Data de Criação   : 13/08/2013
    * @author Analista: Valtair
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AberturaOrcamentoAnual";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro  = new Erro;
$obErroReceitaBruta = new Erro;
$obErroDespesa = new Erro;
$obErroReceitaBruta = new Erro;
$obErroReceitaDedutora = new Erro;
$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$obTContabilidadeLancamento      = new TContabilidadeLancamento;

//Verifica cod_lote
$inCodLoteReceitaBruta = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'M'
                                                                                        and cod_entidade = ".$_POST['inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Orçamento Receita Bruta') ");
$inCodLoteReceitaDedutora = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'M'
                                                                                        and cod_entidade = ".$_POST[ 'inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Orçamento Receita Dedutora') ");
$inCodLoteDespesa = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'M'
                                                                                        and cod_entidade = ".$_POST[ 'inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Orçamento Despesa') ");
$inCodLoteRecurso = SistemaLegado::pegaDado("cod_lote","contabilidade.lote"
                                                            ,"WHERE exercicio = '".Sessao::getExercicio()."'
                                                                AND cod_lote = (SELECT max(cod_lote) FROM contabilidade.lote
                                                                                    WHERE  dt_lote = '".Sessao::getExercicio()."-01-02'
                                                                                        and tipo = 'M'
                                                                                        and cod_entidade = ".$_POST[ 'inCodEntidade']."
                                                                                        and nom_lote = 'Abertura Recursos/Fontes Orçamento') ");
/*
 * Rotina de Inclusao
 */

//Deleta todos os Lancamentos Anteriores de Abertura de Orçamento
$arCodLoteLancamentoAnterior = array($inCodLoteReceitaBruta
                                    ,$inCodLoteReceitaDedutora
                                    ,$inCodLoteDespesa
                                    ,$inCodLoteRecurso);

                                    
foreach ($arCodLoteLancamentoAnterior as $cod_lote) {
    if ($cod_lote != "") {
        $obTContabilidadeLancamento->setDado("cod_entidade" , $_POST['inCodEntidade']);
        $obTContabilidadeLancamento->setDado("cod_lote"     , $cod_lote );
        $obTContabilidadeLancamento->excluiLancamentosAberturaAnteriores($boTransacao);
    }    
}

//--------------------------------------------------/////////////////////////////////////---------------------------------------------------------------
//--------------------------------------------------Receita Bruta Orçada para o Exercício---------------------------------------------------------------
//--------------------------------------------------/////////////////////////////////////---------------------------------------------------------------
$nuValor = 'nuValor_1';
//Verifica o plano de contas selecionado e setas as contas para a consulta de cod_plano e sequencia para atribuir valor digito pelo usuario
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );

//DEBITO
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.1.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaBruta_debito );

//CREDITO
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "6.2.1.1.0.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaBruta_credito );

if ( trim($_POST[$nuValor]) != '' ) {
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor]);
    $nuValorImplantacao = $nuValorImplantacaoReceitaBruta = str_replace(',','.',$nuValorImplantacao);
    $arAberturaOrcamento[$rsReceitaBruta_debito->getCampo( "cod_plano" )."-".$rsReceitaBruta_credito->getCampo( "cod_plano" )]=$nuValorImplantacao;
}

//Monta consulta e operação de lancamento de acordo com o array de cod_plano e sequencia e valores
$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('M');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Orçamento Receita Bruta');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(220);

//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ($inCodLoteReceitaBruta) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteReceitaBruta);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}

//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( $nuValorImplantacao > 0.00 ) {
    $obErroReceitaBruta = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
}elseif( $nuValorImplantacao == 0.00 ){
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroReceitaBruta = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}

//reset do array de dados
$arAberturaOrcamento = array();

//----------------------------------------------------------//////////////////////////////////////////////----------------------------------------
//----------------------------------------------------------Receita Dedutora Bruta Orçada para o Exercício----------------------------------------
//----------------------------------------------------------//////////////////////////////////////////////----------------------------------------
//Verifica o plano de contas selecionado e setas as contas para a consulta de cod_plano e sequencia para atribuir valor digito pelo usuario
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );

//DÉBITO
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "6.2.1.1.0.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora_debito );

//FUNDEB - CRÉDITO
$nuValor2 = 'nuValor_3';
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.2.01.01.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora_credito );
if ( trim($_POST[$nuValor2]) != '' ) {
    $nuSomaValor3       = $_POST[$nuValor2];
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor2]);
    $nuValorImplantacao = $nuValorImplantacaoReceitaDedutora_1 = str_replace(',','.',$nuValorImplantacao);
    $nuSomaValor3       = $nuValorImplantacao;
   
    $arAberturaOrcamento[$rsReceitaDedutora_debito->getCampo( "cod_plano" )."-".$rsReceitaDedutora_credito->getCampo( "cod_plano" )]=$nuValorImplantacao;
}

//RENUNCIA - CRÉDITO
$nuValor3 = 'nuValor_4';
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.2.02.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora_credito );
if ( trim($_POST[$nuValor3]) != '' ) {
    $nuSomaValor4       = $_POST[$nuValor3];
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor3]);
    $nuValorImplantacao = $nuValorImplantacaoReceitaDedutora_2 = str_replace(',','.',$nuValorImplantacao);
    $nuSomaValor4 = $nuValorImplantacao;
    
    $arAberturaOrcamento[$rsReceitaDedutora_debito->getCampo( "cod_plano" )."-".$rsReceitaDedutora_credito->getCampo( "cod_plano" )]=$nuValorImplantacao;
}

//OUTRAS DEDUCOES - CRÉDITO
$nuValor4 = 'nuValor_5';
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.1.1.2.99.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsReceitaDedutora_credito );
if ( trim($_POST[$nuValor4]) != '' ) {
    $nuSomaValor5       = $_POST[$nuValor4];
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor4]);
    $nuValorImplantacao = $nuValorImplantacaoReceitaDedutora_3 = str_replace(',','.',$nuValorImplantacao);
    $nuSomaValor5 = $nuValorImplantacao;
    
    $arAberturaOrcamento[$rsReceitaDedutora_debito->getCampo( "cod_plano" )."-".$rsReceitaDedutora_credito->getCampo( "cod_plano" )]=$nuValorImplantacao;
}

//Monta consulta e operação de lancamento de acordo com o array de cod_plano e sequencia e valores
$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('M');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Orçamento Receita Dedutora');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(222);
//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ($inCodLoteReceitaDedutora) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteReceitaDedutora);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}

//Verifica se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
foreach ($arAberturaOrcamento as $inCodPlano_inCodSequencia => $nuValorLancamento) {
    $arTmp = explode( '-', $inCodPlano_inCodSequencia );
    $inCodPlano     = $arTmp[0];
    $inCodSequencia = $arTmp[2];

    if ($nuValorLancamento == 0.00) {
        //ZERAR QUALQUER LANCAMENTO QUANDO O USUARIO colocar valor = 0.00 deleta da base qualquer lancamento de abertura anterior
        $obTContabilidadeLancamento->setDado("cod_lote"     , $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() );
        $obTContabilidadeLancamento->setDado("cod_entidade" , $_POST['inCodEntidade'] );
        $obTContabilidadeLancamento->excluiLancamentosAberturaAnteriores($boTransacao);
        unset($arAberturaOrcamento[$inCodPlano_inCodSequencia]);
    }
}
//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( ($nuSomaValor3+$nuSomaValor4+$nuSomaValor5) > 0) {
    $obErroReceitaDedutora = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
}elseif( $nuValorImplantacao == 0 ){
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroReceitaDedutora = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}
//$obErroReceitaDedutora = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);

//reset do array de dados
$arAberturaOrcamento = array();


//--------------------------------------------------------------/////////////////////////////////-----------------------------------------------------
//--------------------------------------------------------------Despesa Prevista para o Exercício-----------------------------------------------------
//--------------------------------------------------------------/////////////////////////////////-----------------------------------------------------
$nuValor = 'nuValor_6';
//Verifica o plano de contas selecionado e setas as contas para a consulta de cod_plano e sequencia para atribuir valor digito pelo usuario
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setExercicio    ( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST[ 'inCodEntidade' ] );

//DÉBITO
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "5.2.2.1.1.01.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsDespesaPrevista_debito );

//CRÉDITO
$obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "6.2.2.1.1.00.00.00.00.00" );
$obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsDespesaPrevista_credito );

if ( trim($_POST[$nuValor]) != '' ) {
    $nuValorImplantacao = str_replace('.','',$_POST[$nuValor]);
    $nuValorImplantacao = $nuValorImplantacaoDespesaPrevista = str_replace(',','.',$nuValorImplantacao);
    
    $arAberturaOrcamento[$rsDespesaPrevista_debito->getCampo( "cod_plano" )."-".$rsDespesaPrevista_credito->getCampo( "cod_plano" )]=$nuValorImplantacao;
}

//Monta consulta e operação de lancamento de acordo com o array de cod_plano e sequencia e valores
$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('M');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Orçamento Despesa');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(221);
//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ( $inCodLoteDespesa ) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteDespesa);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}

//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( $nuValorImplantacao > 0.00 ) {
    $obErroDespesa = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
}elseif( $nuValorImplantacao == 0.00 ){
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroDespesa = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}

//reset do array de dados
$arAberturaOrcamento = array();

//--------------------------------------------------------------///////////////////////////////////////////-------------------------------------------
//--------------------------------------------------------------LANÇAMENTOS DE ABERTURA DOS RECURSOS/FONTES-------------------------------------------
//--------------------------------------------------------------///////////////////////////////////////////-------------------------------------------
//LANÇAMENTOS DE ABERTURA DOS RECURSOS/FONTES
//busca os saldos iniciais de recursos
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoBanco.class.php';
$obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
$obTContabilidadePlanoBanco->setDado( 'exercicio',Sessao::getExercicio() );
$obTContabilidadePlanoBanco->recuperaSaldoInicialRecurso($rsRecursos);

$nuValorImplantacao=0;

$rsRecursos->setPrimeiroElemento();
while ( !$rsRecursos->eof() ) {

    //DEBITO
    $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "7.2.1.1.1" );
    $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodRecurso($rsRecursos->getCampo('cod_recurso'));
    $obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsRecursoFonte_debito );
    
    //CREDITO
    $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodEstrutural( "8.2.1.1.1" );
    $obRContabilidadeLancamentoValor->obRContabilidadePlanoContaAnalitica->setCodRecurso($rsRecursos->getCampo('cod_recurso'));
    $obRContabilidadeLancamentoValor->listarLoteImplantacao( $rsRecursoFonte_credito );
    
    $arAberturaOrcamento[$rsRecursoFonte_debito->getCampo( "cod_plano" )."-".$rsRecursoFonte_credito->getCampo( "cod_plano" )] = $rsRecursos->getCampo('saldo');
    
    $nuValorImplantacao = $nuValorImplantacao + $rsRecursos->getCampo('saldo');
    $rsRecursos->proximo();
    $i++;
}

$obRContabilidadeLancamentoValor->setAberturaOrcamento($arAberturaOrcamento);
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( "02/01/".Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo('M');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote('Abertura Recursos/Fontes Orçamento');
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico(223);

//verifica se ja existe algum lote de abertura se não ele pega o proximo codigo de lote
if ($inCodLoteRecurso) {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLoteRecurso);
} else {
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->buscaProximoCodigo();
    $inCodLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote();
    $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($inCodLote);
}

//se o valor for maior que zero ele faz o lancamento, se for 0.00 ele zera os lancamentos anteriores
if ( (($nuValorImplantacaoReceitaBruta+$nuValorImplantacaoReceitaDedutora_1+$nuValorImplantacaoReceitaDedutora_2+$nuValorImplantacaoReceitaDedutora_3+$nuValorImplantacaoDespesaPrevista) > 0 ) && ($nuValorImplantacao > 0)) {
    $obErroRecurso = $obRContabilidadeLancamentoValor->aberturaOrcamento($boTransacao);        
//}elseif( $nuValorImplantacao == 0.00 ){
} else {
    //verifica lote que ja foi aberto
    if ( $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getCodLote() ) {
        $obErroRecurso = $obRContabilidadeLancamentoValor->excluirLancamento($boTransacao);
    }
}

//verifica se ocorreu erro em todos os lancamentos
if( !$obErroRecurso->ocorreu()
    && !$obErroDespesa->ocorreu()
    && !$obErroReceitaBruta->ocorreu()
    && !$obErroReceitaDedutora->ocorreu()
    && !$obErro->ocorreu()
    ){
        SistemaLegado::alertaAviso($pgForm, "1 - ".($obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia() ? $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia() : "0")."", "incluir", "aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}
