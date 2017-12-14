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
	* Página do Oculto de Cadastro de Contratos TCEMG
	* Data de Criação   : 21/02/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: OCManterContrato.php 64526 2016-03-09 19:10:59Z jean $
*/

include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'     );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php'  );

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

function montaListaEmpenhos()
{
    global $stAcao;
    $obLista = new Lista;
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arEmpenhos') );

    $obLista->setRecordset( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( 'Lista de empenhos' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Entidade");
    $obLista->ultimoCabecalho->setWidth( 5);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 10);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome do Credor");
    $obLista->ultimoCabecalho->setWidth( 80 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirEmpenhoLista');" );
    $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codEntidade=[cod_entidade]&stExercicio=[exercicio]&stAcao=".$stAcao);
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";

    return $stJs;
}

function MontaFornecedores()
{
    $obLista = new Lista;
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arFornecedores') );

    $obLista->setRecordset( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( 'Lista de Fornecedores' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Fornecedor");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM do Representante Legal");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodFornecedor] - [stNomCGM]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cgmRepLegal] - [stNomRepLegal]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirFornecedorLista');" );
    $obLista->ultimaAcao->addCampo("","&codFornecedor=[inCodFornecedor]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnFornecedor').innerHTML = '';\n";
    if(count(Sessao::read('arFornecedores'))>0){
        $stJs .= "d.getElementById('spnFornecedor').innerHTML = '".$html."';\n";
    }

    return $stJs;
}

function MontaModalidade($ent)
{
    include_once ( '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php' );
    include_once ( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoOrgao.class.php' );
    
    $obTOrgao	 = new TOrcamentoOrgao();
    $obTOrgao->recuperaRelacionamento($rsOrgao, " AND OO.exercicio = '" . $_REQUEST["stExercicioContrato"]. "'", ' ORDER BY OO.nom_orgao');

    //Início Select Entidade Modalidade
    $obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
    $obEntidadeUsuario->setNull                 ( false                         );
    $obEntidadeUsuario->setRotulo               ( "Entidade Modalidade"         );
    $obEntidadeUsuario->obTextBox->setSize      ( 3                             );
    $obEntidadeUsuario->obTextBox->setMaxLength ( 1                             );
    $obEntidadeUsuario->obTextBox->setName      ( "inCodEntidadeModalidadeTxt"  );
    $obEntidadeUsuario->obTextBox->setId        ( "inCodEntidadeModalidadeTxt"  );
    $obEntidadeUsuario->obSelect->setName       ( "inCodEntidadeModalidade"     );
    $obEntidadeUsuario->obSelect->setId         ( "inCodEntidadeModalidade"     );
    
    //Início Select Orgão Modalidade
    $obTxtOrgao = new TextBoxSelect;
    $obTxtOrgao->setRotulo                  ( "Orgão Modalidade"                );
    $obTxtOrgao->setTitle                   ( "Selecione o orgão para filtro."  );
    $obTxtOrgao->obTextBox->setName         ( "inCodOrgaoModalidadeTxt"         );
    $obTxtOrgao->obTextBox->setId           ( "inCodOrgaoModalidadeTxt"         );
    $obTxtOrgao->obTextBox->setValue        ( $inCodOrgaoModalidadeTxt          );
    $obTxtOrgao->obTextBox->setSize         ( 3                                 );
    $obTxtOrgao->obTextBox->setMaxLength    ( 2                                 );
    $obTxtOrgao->obTextBox->setInteiro      ( true                              );
    $obTxtOrgao->setObrigatorio             ( true                              );
    
    $obTxtOrgao->obTextBox->obEvento->setOnChange("montaParametrosGET('MontaUnidadeModalidade');");
    $obTxtOrgao->obSelect->obEvento->setOnChange ("montaParametrosGET('MontaUnidadeModalidade');");
    
    $obTxtOrgao->obSelect->setStyle         ( "width: 520"                  );
    $obTxtOrgao->obSelect->setCampoID       ( 'num_orgao'                   );
    $obTxtOrgao->obSelect->setCampoDesc     ( 'nom_orgao'                   );
    $obTxtOrgao->obSelect->addOption        ( '', 'Selecione'               );
    $obTxtOrgao->obSelect->setName          ( "inCodOrgaoModalidade"        );
    $obTxtOrgao->obSelect->setId            ( "inCodOrgaoModalidade"        );
    $obTxtOrgao->obSelect->preencheCombo    ( $rsOrgao                      );
    $obTxtOrgao->setMensagem                ( "Orgão inválido"              );
    
    
    //Início Select Unidade Modalidade
    $obTxtUnidade = new TextBoxSelect;
    $obTxtUnidade->setRotulo                ( "Unidade Modalidade"          );
    $obTxtUnidade->setTitle                 ( "Selecione a unidade."        );
    $obTxtUnidade->obTextBox->setName       ( "inCodUnidadeModalidadeTxt"   );
    $obTxtUnidade->obTextBox->setId         ( "inCodUnidadeModalidadeTxt"   );
    $obTxtUnidade->obTextBox->setValue      ( $inCodUnidadeTxt              );
    $obTxtUnidade->obTextBox->setSize       ( 3                             );
    $obTxtUnidade->obTextBox->setMaxLength  ( 2                             );
    $obTxtUnidade->obTextBox->setInteiro    ( true                          );
    $obTxtUnidade->setObrigatorio           ( true                          );
    
    $obTxtUnidade->obSelect->setRotulo      ( "Unidade Modalidade"          );
    $obTxtUnidade->obSelect->setName        ( "inCodUnidadeModalidade"      );
    $obTxtUnidade->obSelect->setId          ( "inCodUnidadeModalidade"      );
    $obTxtUnidade->obSelect->setValue       ( $inCodUnidadeModalidade       );
    $obTxtUnidade->obSelect->setStyle       ( "width: 520"                  );
    $obTxtUnidade->obSelect->setCampoID     ( "cod_unidade"                 );
    $obTxtUnidade->obSelect->setCampoDesc   ( "descricao"                   );
    $obTxtUnidade->obSelect->addOption      ( "", "Selecione"               );
    $obTxtUnidade->obSelect->setObrigatorio ( true                          );
    
    //Início TextBox Numero do Processo
    $obTxtNroProcesso = new TextBox;
    $obTxtNroProcesso->setName              ( "inNumProcesso"                   );
    $obTxtNroProcesso->setId                ( "inNumProcesso"                   );
    $obTxtNroProcesso->setValue             ( $_REQUEST['inNumProcesso']        );
    $obTxtNroProcesso->setRotulo            ( "Número do Processo"              );
    $obTxtNroProcesso->setTitle             ( "Informe o número do processo."   );
    $obTxtNroProcesso->setNull              ( false                             );
    $obTxtNroProcesso->setInteiro           ( true                              );
    $obTxtNroProcesso->setMaxLength         ( 5                                 );
    
    //Início TextBox Exercicio do Processo
    $obTxtExercicioProcesso = new TextBox;
    $obTxtExercicioProcesso->setName        ( "stExercicioProcesso"             );
    $obTxtExercicioProcesso->setId          ( "stExercicioProcesso"             );
    $obTxtExercicioProcesso->setValue       (  $stAnoExercicio                  );
    $obTxtExercicioProcesso->setRotulo      ( "Exercício Processo"              );
    $obTxtExercicioProcesso->setTitle       ( "Informe o exercício do processo.");
    $obTxtExercicioProcesso->setInteiro     ( false                             );
    $obTxtExercicioProcesso->setNull        ( false                             );
    $obTxtExercicioProcesso->setMaxLength   ( 4                                 );
    $obTxtExercicioProcesso->setSize        ( 5                                 );
    
    
    //Início Select Tipo do Processo, quando dispensa ou inexigibilidade
    include_once( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGContratoTipoProcesso.class.php" );
    $obTTCEMGContratoTipoProcesso = new TTCEMGContratoTipoProcesso;
    $stOrder = " ORDER BY descricao ";
    $obTTCEMGContratoTipoProcesso->recuperaTodos($rsTipo, "", $stOrder);
    
    $obCmbTipoContrato = new Select;
    $obCmbTipoContrato->setName      ( "cod_tipo_processo"  );
    $obCmbTipoContrato->setRotulo    ( "Tipo de Processo"   );
    $obCmbTipoContrato->setId        ( "stTipoProcesso"     );
    $obCmbTipoContrato->setCampoId   ( "cod_tipo_processo"  );
    $obCmbTipoContrato->setCampoDesc ( "descricao"          );
    $obCmbTipoContrato->addOption    ( '','Selecione'       );
    $obCmbTipoContrato->preencheCombo( $rsTipo              );
    $obCmbTipoContrato->setNull      ( false                );
    $obCmbTipoContrato->setValue     ( ''                   );
    
    //Início do Formulário
    $obFormulario = new Formulario;
    $obFormulario->obForm->setName("lic");
    if($ent==5||$ent==6){
        $obFormulario->addComponente    ( $obEntidadeUsuario );
        $obFormulario->addComponente    ( $obTxtOrgao        );
        $obFormulario->addComponente    ( $obTxtUnidade      );
    }
    if($ent==3||$ent==6){
       $obFormulario->addComponente     ( $obCmbTipoContrato ); 
    }
    if($ent>1){
        $obFormulario->addComponente    ( $obTxtNroProcesso         );
        $obFormulario->addComponente    ( $obTxtExercicioProcesso   );
    }
    $obFormulario->montaInnerHTML();
    
    if($ent>1){
        $stJs= "jQuery('#spnEntidadeLicitacao').html('".$obFormulario->getHTML()."');";
    }else{
        $stJs= "jQuery('#spnEntidadeLicitacao').html('');";
    }
    
    return $stJs;
}

function MontaFormaNatureza()
{
    $obTxtFornecimento = new TextBox;
    $texto = "Descrição da forma de fornecimento ou regime de execução,conforme previsão do art. 55, II, da Lei Federal n. 8.666/93.";
    $obTxtFornecimento->setRotulo   ( "Forma de Fornecimento ou Regime de Execução" );
    $obTxtFornecimento->setName     ( "stFormaFornecimento" );
    $obTxtFornecimento->setId       ( "stFormaFornecimento" );
    $obTxtFornecimento->setTitle    ( $texto                );
    $obTxtFornecimento->setNull     ( false                 );
    $obTxtFornecimento->setInteiro  ( false                 );
    $obTxtFornecimento->setMaxLength( 50                    );
    $obTxtFornecimento->setSize     ( 50                    );
    
    $obTxtPagamento = new TextBox;
    $texto = "Descrever o preço e as condições de pagamento, os critérios, data-base e periodicidade do reajustamento de preços,
    os critérios de atualização monetária entre a data do adimplemento das obrigações e a do efetivo pagamento,
    conforme previsão do art. 55, III, da Lei Federal n. 8.666/93.";
    $obTxtPagamento->setName     ( "stFormaPagamento"   );
    $obTxtPagamento->setId       ( "stFormaPagamento"   );
    $obTxtPagamento->setRotulo   ( "Forma de Pagamento" );
    $obTxtPagamento->setTitle    ( $texto               );
    $obTxtPagamento->setNull     ( false                );
    $obTxtPagamento->setInteiro  ( false                );
    $obTxtPagamento->setMaxLength( 100                  );
    $obTxtPagamento->setSize     ( 90                   );
    
    $obTxtPrazo = new TextBox;
    $texto = "Descrever os prazos de início de etapas de execução, de conclusão, 
    de entrega, de observação e de recebimento definitivo, conforme o caso,
    de acordo com a previsão do art. 55, IV, da Lei Federal n. 8.666/93.";
    $obTxtPrazo->setName     ( "stFormaPrazo"       );
    $obTxtPrazo->setId       ( "stFormaPrazo"       );
    $obTxtPrazo->setRotulo   ( "Prazo de Execução"  );
    $obTxtPrazo->setTitle    ( $texto               );
    $obTxtPrazo->setNull     ( false                );
    $obTxtPrazo->setInteiro  ( false                );
    $obTxtPrazo->setMaxLength( 100                  );
    $obTxtPrazo->setSize     ( 90                   );
    
    $obTxtMulta = new TextBox;
    $texto = "Descrição da previsão de multa rescisória, conforme previsão do art. 55, VII, da Lei Federal n. 8.666/93.";
    $obTxtMulta->setName     ( "stFormaMulta"       );
    $obTxtMulta->setId       ( "stFormaMulta"       );
    $obTxtMulta->setRotulo   ( "Multa Rescisória"   );
    $obTxtMulta->setTitle    ( $texto               );
    $obTxtMulta->setNull     ( false                );
    $obTxtMulta->setInteiro  ( false                );
    $obTxtMulta->setMaxLength( 100                  );
    $obTxtMulta->setSize     ( 90                   );
    
    $obTxtMultaInadimplemento = new TextBox;
    $texto = "Descrição da previsão de multa por inadimplemento, conforme previsão do art. 55, VII, da Lei Federal n. 8.666/93.";
    $obTxtMultaInadimplemento->setName     ( "stMultaInadimplemento"	);
    $obTxtMultaInadimplemento->setId       ( "stMultaInadimplemento"	);
    $obTxtMultaInadimplemento->setRotulo   ( "Multa Inadimplemento"	);
    $obTxtMultaInadimplemento->setTitle    ( $texto               	);
    $obTxtMultaInadimplemento->setNull     ( false                	);
    $obTxtMultaInadimplemento->setInteiro  ( false                	);
    $obTxtMultaInadimplemento->setMaxLength( 100                  	);
    $obTxtMultaInadimplemento->setSize     ( 90                   	);
    
    include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoGarantia.class.php' );
    $obTTCEMGContratoGarantia = new TTCEMGContratoGarantia;
    $stOrder = " ORDER BY descricao ";
    $obTTCEMGContratoGarantia->recuperaTodos($rsGarantia, "", $stOrder);
    
    $obCmbGarantia = new Select;
    $texto = "Selecione Garantia, de acordo com o art. 55, VI, da Lei Federal n. 8.666/93.";
    $obCmbGarantia->setName      ( "cod_garantia"               );
    $obCmbGarantia->setRotulo    ( "Tipo de Garantia Contratual");
    $obCmbGarantia->setTitle     ( $texto                       );
    $obCmbGarantia->setId        ( "stGarantia"                 );
    $obCmbGarantia->setCampoId   ( "cod_garantia"               );
    $obCmbGarantia->setCampoDesc ( "descricao"                  );
    $obCmbGarantia->addOption    ( '','Selecione'               );
    $obCmbGarantia->preencheCombo( $rsGarantia                  );
    $obCmbGarantia->setNull      ( false                        );
    $obCmbGarantia->setValue     ( ''                           );
    
    //Início do Formulário
    $obFormulario = new Formulario;
    $obFormulario->obForm->setName  ("forma");
    $obFormulario->addComponente    ( $obTxtFornecimento );
    $obFormulario->addComponente    ( $obTxtPagamento    );
    $obFormulario->addComponente    ( $obTxtPrazo        );
    $obFormulario->addComponente    ( $obTxtMulta        );
    $obFormulario->addComponente    ( $obTxtMultaInadimplemento );
    $obFormulario->addComponente    ( $obCmbGarantia     );
    $obFormulario->montaInnerHTML   ();
    
    $stJs= "jQuery('#spnFormaNatureza').html('".$obFormulario->getHTML()."');";
    
    return $stJs;
}

function MontaUnidade($inCodOrgao, $Aditivo='')
{        
        include_once    ( CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php' );
        $obROrcamentoDespesa = new ROrcamentoDespesa;
        if( $inCodOrgao ){
            $stCombo  = "inCodUnidade".$Aditivo;
            $stComboTxt  = "inCodUnidadeTxt".$Aditivo;
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inCodOrgao);
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setExercicio($_REQUEST["stExercicioContrato"]);
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->listar( $rsCombo, " ORDER BY num_unidade");
            $inCount = 0;

            while(!$rsCombo->eof()){
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }
    $stJs .= $js;

    return $stJs;
}

function MontaUnidadeModalidade($inCodOrgaoModalidade)
{
        include_once    ( CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php' );
        $obROrcamentoDespesa = new ROrcamentoDespesa;
        if( $inCodOrgaoModalidade ){
            $stCombo  = "inCodUnidadeModalidade";
            $stComboTxt  = "inCodUnidadeModalidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inCodOrgaoModalidade);
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setExercicio($_REQUEST["stExercicioContrato"]);
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->listar( $rsCombo, " ORDER BY num_unidade");
            $inCount = 0;

            while(!$rsCombo->eof()){
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }
    $stJs .= $js;

    return $stJs;
}

switch( $stCtrl ){
   
    case "carregaDados":
        if( isset($_REQUEST['inNumContrato']) && isset($_REQUEST['exercicio_contrato']) && isset($_REQUEST['inCodContrato']) && $_REQUEST['inCodContrato']!='' ){
            //Recupera Contrato            
            include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContrato.class.php' );
            $obTTCEMGContrato = new TTCEMGContrato;
            $stFiltro  = " WHERE nro_contrato   =  ".$_REQUEST['inNumContrato'];
            $stFiltro .= " AND exercicio        = '".$_REQUEST['exercicio_contrato']."'";
            $stFiltro .= " AND cod_entidade     = '".$_REQUEST['cod_entidade']."'";
            $obTTCEMGContrato->recuperaTodos($rsContrato, $stFiltro);

	    //Recupera Contrato-Empenho
            include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoEmpenho.class.php' );
            $obTTCEMGContratoEmpenho = new TTCEMGContratoEmpenho;
            $stFiltro  = " WHERE cod_contrato   =  ".$rsContrato->getCampo('cod_contrato');
            $stFiltro .= " AND exercicio        = '".$rsContrato->getCampo('exercicio')."'"; 
            $stFiltro .= " AND cod_entidade     = '".$_REQUEST['cod_entidade']."'";
            $obTTCEMGContratoEmpenho->recuperaTodos($rsEmpenhoContrato, $stFiltro);
    
            $arEmpenhos = array();
            $inCount = 0;
    
            include_once( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php' );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            while( !$rsEmpenhoContrato->eof()){
                $stFiltro  = " AND e.exercicio    = '".$rsEmpenhoContrato->getCampo('exercicio_empenho')."'";
                $stFiltro .= " AND e.cod_entidade =  ".$rsEmpenhoContrato->getCampo('cod_entidade');
                $stFiltro .= " AND e.cod_empenho  =  ".$rsEmpenhoContrato->getCampo('cod_empenho');
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsEmpenho, $stFiltro);
    
                $arEmpenhos[$inCount]['cod_entidade'] = $rsEmpenho->getCampo('cod_entidade' );
                $arEmpenhos[$inCount]['cod_empenho']  = $rsEmpenho->getCampo('cod_empenho'  );
                $arEmpenhos[$inCount]['exercicio']    = $rsEmpenho->getCampo('exercicio'    );
                $arEmpenhos[$inCount]['nom_cgm']      = $rsEmpenho->getCampo('credor'	    );
    
                $inCount++;
                $rsEmpenhoContrato->proximo();
            }
            
	    //Recupera Contrato-Fornecedor
            include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoFornecedor.class.php' );
            $obTTCEMGContratoFornecedor = new TTCEMGContratoFornecedor;
            $stFiltro  = " WHERE cod_contrato   =  ".$rsContrato->getCampo('cod_contrato');
            $stFiltro .= " AND exercicio        = '".$rsContrato->getCampo('exercicio')."'"; 
            $stFiltro .= " AND cod_entidade     = '".$_REQUEST['cod_entidade']."'";
            $obTTCEMGContratoFornecedor->recuperaTodos($rsFornecedor, $stFiltro);
    
            $arFornecedores = array();
            $inCount = 0;
    
            while( !$rsFornecedor->eof()){
                $where = " WHERE numcgm=".$rsFornecedor->getCampo('cgm_fornecedor');
                $nomcgm_fornecedor = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', $where);
                
                $where = " WHERE numcgm=".$rsFornecedor->getCampo('cgm_representante');
                $nomcgm_representante = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', $where);
    
                $arFornecedores[$inCount]['inCodFornecedor']	= $rsFornecedor->getCampo('cgm_fornecedor');
                $arFornecedores[$inCount]['stNomCGM']  	        = $nomcgm_fornecedor;
                
                $arFornecedores[$inCount]['cgmRepLegal']        = $rsFornecedor->getCampo('cgm_representante');
                $arFornecedores[$inCount]['stNomRepLegal']      = $nomcgm_representante;
    
                $inCount++;
                $rsFornecedor->proximo();
            }
            
            //Recupera Contrato-Aditivo
            include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivo.class.php' );
            include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoAditivoItem.class.php' );
            $obTTCEMGContratoAditivo = new TTCEMGContratoAditivo;
            $obTTCEMGContratoAditivoItem = new TTCEMGContratoAditivoItem;
            
            $stFiltro  = " WHERE cod_contrato           = ".$rsContrato->getCampo('cod_contrato');
            $stFiltro .= " AND exercicio_contrato       = '".$rsContrato->getCampo('exercicio')."'"; 
            $stFiltro .= " AND cod_entidade_contrato    = ".$_REQUEST['cod_entidade'];
            $obTTCEMGContratoAditivo->recuperaTodos($rsAditivo, $stFiltro);
    
            $arAditivo = array();
            $inCount = 0;
    
            while( !$rsAditivo->eof()){                
                $arAditivo[$inCount]['stExercicioAditivo']   = $rsAditivo->getCampo('exercicio');
                $arAditivo[$inCount]['inCodEntidadeAditivo'] = $rsAditivo->getCampo('cod_entidade');
                $arAditivo[$inCount]['inCodOrgaoAditivo']    = $rsAditivo->getCampo('num_orgao');
                $arAditivo[$inCount]['inCodUnidadeAditivo']  = $rsAditivo->getCampo('num_unidade');
                $arAditivo[$inCount]['inCodAditivo']         = $rsAditivo->getCampo('nro_aditivo');
                $arAditivo[$inCount]['inCodTermoAditivo']    = $rsAditivo->getCampo('cod_tipo_aditivo');
                $arAditivo[$inCount]['inVeiculoAditivo']     = $rsAditivo->getCampo('cgm_publicacao');
                $arAditivo[$inCount]['dtPublicacaoAditivo']  = $rsAditivo->getCampo('data_publicacao');
                $arAditivo[$inCount]['dtAssinaturaAditivo']  = $rsAditivo->getCampo('data_assinatura');
                $arAditivo[$inCount]['vlTotal']              = number_format($rsAditivo->getCampo('valor'),2,',','.');
                $arAditivo[$inCount]['inTipoValor']         = $rsAditivo->getCampo('cod_tipo_valor');
                if($rsAditivo->getCampo('descricao')!=''){
                    $arAditivo[$inCount]['stDescricaoAditivo']  = $rsAditivo->getCampo('descricao');
                }
                if($rsAditivo->getCampo('data_termino')!=''){
                    $arAditivo[$inCount]['dtTerminoAditivo']    = $rsAditivo->getCampo('data_termino');
                }
                
                $stFiltro  = " WHERE cod_contrato_aditivo   = ".$rsAditivo->getCampo('cod_contrato_aditivo');
                $stFiltro .= " AND exercicio                = '".$rsAditivo->getCampo('exercicio')."'"; 
                $stFiltro .= " AND cod_entidade             = ".$rsAditivo->getCampo('cod_entidade');
                $obTTCEMGContratoAditivoItem->recuperaTodos($rsAditivoItem, $stFiltro);
                $count=0;
                
                while( !$rsAditivoItem->eof()){
                    $where  = "  WHERE exercicio    = '".$rsAditivoItem->getCampo('exercicio_pre_empenho');
                    $where .= "' AND cod_pre_empenho=  ".$rsAditivoItem->getCampo('cod_pre_empenho');
                    $where .= "  AND num_item       =  ".$rsAditivoItem->getCampo('num_item');
                    $vlTotalItem = SistemaLegado::pegaDado('vl_total', 'empenho.item_pre_empenho', $where);
                    $quantidadeItem = SistemaLegado::pegaDado('quantidade', 'empenho.item_pre_empenho', $where);
                    $vlUnitario = $vlTotalItem/$quantidadeItem;
                    
                    $arAditivo[$inCount]['itens'][$count]['cod_empenho']    = $rsAditivoItem->getCampo('cod_empenho');
                    $arAditivo[$inCount]['itens'][$count]['exercicio']      = $rsAditivoItem->getCampo('exercicio_empenho');
                    $arAditivo[$inCount]['itens'][$count]['cod_entidade']   = $rsAditivoItem->getCampo('cod_entidade');
                    $arAditivo[$inCount]['itens'][$count]['num_item']       = $rsAditivoItem->getCampo('num_item');
                    $arAditivo[$inCount]['itens'][$count]['vl_unitario']    = $vlUnitario;
                    $arAditivo[$inCount]['itens'][$count]['quantidade']     = $rsAditivoItem->getCampo('quantidade');
                    if($rsAditivoItem->getCampo('tipo_acresc_decresc')!=''){
                        $arAditivo[$inCount]['itens'][$count]['tipo']       = $rsAditivoItem->getCampo('tipo_acresc_decresc');
                    }
                    
                    $count++;
                    $rsAditivoItem->proximo();
                }
    
                $inCount++;
                $rsAditivo->proximo();
            }

            $stJs .= "f.stObjContrato.value  = '".stripslashes($rsContrato->getCampo('objeto_contrato'))."';\n";
            $stJs .= "f.dtInicial.value      = '".$rsContrato->getCampo('data_inicio')."';\n";
            $stJs .= "f.dtFinal.value        = '".$rsContrato->getCampo('data_final')."';\n";
            $stJs .= "f.nuVlContrato.value   = '".number_format($rsContrato->getCampo('vl_contrato'),2,',','.')."';\n";
            $stJs .= "f.dtAssinatura.value        = '".$rsContrato->getCampo('data_assinatura')."';\n";
            $stJs .= "f.inCodContrato.value  = '".$rsContrato->getCampo('cod_contrato')."';\n";
            $stJs .= "f.inCodEntidade.value  = '".$rsContrato->getCampo('cod_entidade')."';\n";
            $stJs .= "jQuery('#stNomEntidade option[value=".$rsContrato->getCampo('cod_entidade')."]').attr('selected', 'selected');\n";
            $stJs .= "f.inCodOrgaoTxt.value  = '".$rsContrato->getCampo('num_orgao')."';\n";
            $stJs .= "jQuery('#inCodBanco option[value=".$rsContrato->getCampo('num_orgao')."]').attr('selected', 'selected');\n";
            $stJs .= MontaUnidade($rsContrato->getCampo('num_orgao'));
            $stJs .= "f.inCodUnidadeTxt.value = '".$rsContrato->getCampo('num_unidade')."';\n";
            $stJs .= "jQuery('#inCodUnidade option[value=".$rsContrato->getCampo('num_unidade')."]').attr('selected', 'selected');\n";
            $stJs .= "f.stNomModLic.value   = '".$rsContrato->getCampo('cod_modalidade_licitacao')."';\n";
            $stJs .= MontaModalidade($rsContrato->getCampo('cod_modalidade_licitacao'));

            if($rsContrato->getCampo('cod_entidade_modalidade')!=''){
               $stJs .= "f.inCodEntidadeModalidadeTxt.value   = '".$rsContrato->getCampo('cod_entidade_modalidade')."';\n";
               $stJs .= "jQuery('#inCodEntidadeModalidade option[value=".$rsContrato->getCampo('cod_entidade_modalidade')."]').attr('selected', 'selected');\n";
            }

            if($rsContrato->getCampo('num_orgao_modalidade')!=''){
               $stJs .= "f.inCodOrgaoModalidadeTxt.value   = '".$rsContrato->getCampo('num_orgao_modalidade')."';\n";
               $stJs .= "jQuery('#inCodOrgaoModalidade option[value=".$rsContrato->getCampo('num_orgao_modalidade')."]').attr('selected', 'selected');\n";
               $stJs .= MontaUnidadeModalidade($rsContrato->getCampo('num_orgao_modalidade'));
            }

            if($rsContrato->getCampo('num_unidade_modalidade')!=''){
               $stJs .= "f.inCodUnidadeModalidadeTxt.value   = '".$rsContrato->getCampo('num_unidade_modalidade')."';\n";
               $stJs .= "jQuery('#inCodUnidadeModalidade option[value=".$rsContrato->getCampo('num_unidade_modalidade')."]').attr('selected', 'selected');\n";
            }

            if($rsContrato->getCampo('cod_tipo_processo')!=''){
               $stJs .= "f.stTipoProcesso.value   = '".$rsContrato->getCampo('cod_tipo_processo')."';\n";
            }

            if($rsContrato->getCampo('nro_processo')!=''){
               $stJs .= "f.inNumProcesso.value   = '".$rsContrato->getCampo('nro_processo')."';\n";
            }

            if($rsContrato->getCampo('exercicio_processo')!=''){
               $stJs .= "f.stExercicioProcesso.value   = '".$rsContrato->getCampo('exercicio_processo')."';\n";
            }

            $stJs .= "f.stObjeto.value   = '".$rsContrato->getCampo('cod_objeto')."';\n";

            if($rsContrato->getCampo('cod_objeto')>=1 && $rsContrato->getCampo('cod_objeto')<4){
                $stJs .= MontaFormaNatureza();
            }

            $stJs .= "f.stInstrumento.value   = '".$rsContrato->getCampo('cod_instrumento')."';\n";
            
            if($rsContrato->getCampo('fornecimento')!=''){
               $stJs .= "f.stFormaFornecimento.value   = '".$rsContrato->getCampo('fornecimento')."';\n";
            }
            if($rsContrato->getCampo('pagamento')!=''){
               $stJs .= "f.stFormaPagamento.value   = '".$rsContrato->getCampo('pagamento')."';\n";
            }

            if($rsContrato->getCampo('execucao')!=''){
               $stJs .= "f.stFormaPrazo.value   = '".$rsContrato->getCampo('execucao')."';\n";
            }

            if($rsContrato->getCampo('multa')!=''){
               $stJs .= "f.stFormaMulta.value   = '".$rsContrato->getCampo('multa')."';\n";
            }

	    if($rsContrato->getCampo('multa_inadimplemento')!=''){
               $stJs .= "f.stMultaInadimplemento.value   = '".$rsContrato->getCampo('multa_inadimplemento')."';\n";
            }

            if($rsContrato->getCampo('cod_garantia')!=''){
               $stJs .= "f.stGarantia.value   = '".$rsContrato->getCampo('cod_garantia')."';\n";
            }
            
            $stJs .= "f.cgmSignatario.value   = '".$rsContrato->getCampo('cgm_signatario')."';\n";
            $where = " WHERE numcgm=".$rsContrato->getCampo('cgm_signatario');
            $nomcgm_signatario = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', $where);
            $stJs .= "d.getElementById('stNomSignatario').innerHTML   = '".$nomcgm_signatario."';\n";
            
            $stJs .= "f.inVeiculo.value   = '".$rsContrato->getCampo('numcgm_publicidade')."';\n";
            $where = " WHERE numcgm=".$rsContrato->getCampo('numcgm_publicidade');
            $nomcgm_publicidade = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', $where);
            $stJs .= "d.getElementById('stNomCgmVeiculoPublicadade').innerHTML   = '".$nomcgm_publicidade."';\n";
            
            $stJs .= "f.dtPublicacao.value   = '".$rsContrato->getCampo('data_publicacao')."';\n";
    
            $stJs .= "f.btnLimparEmp.disabled  = false; ";
            $stJs .= "f.btnIncluirEmp.disabled = false; ";    
            $stJs .= "f.inCodEntidade.disabled = true;  ";
            $stJs .= "f.stNomEntidade.disabled = true;  ";
	    $stJs .= "f.stExercicioContrato.disabled = true; ";
            
            Sessao::write('arEmpenhos', $arEmpenhos);
            $stJs .= montaListaEmpenhos();
            
            Sessao::write('arFornecedores', $arFornecedores);
            $stJs .= MontaFornecedores();
            
            Sessao::write('arAditivo', $arAditivo);
        }

        echo $stJs;
    break;    

    case "limpaCampoEmpenho":
        $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
        $stJs .= "f.numEmpenho.value = '';";

        echo $stJs;
    break; 

    case "incluirEmpenhoLista":
        $arRegistro = array();
        $arEmpenhos = array();
        $arRequest  = array();
        $arRequest  = explode('/', $_REQUEST['numEmpenho']);
        $boIncluir  = true; 
    
        $arEmpenhos = Sessao::read('arEmpenhos');

        if( $_REQUEST['stExercicioEmpenho'] and $arRequest[0] != "" ){
       
            include_once( CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGContratoEmpenho.class.php' );
            $obTTCEMGContratoEmpenho = new TTCEMGContratoEmpenho;
            $stFiltro  = " WHERE cod_empenho       =  ".$arRequest[0];
            $stFiltro .= "   AND cod_entidade      =  ".$_REQUEST['inCodEntidade'];
            $stFiltro .= "   AND exercicio_empenho = '".$_REQUEST['stExercicioEmpenho']."'";
            $obTTCEMGContratoEmpenho->recuperaTodos($rsEmpenhoContrato, $stFiltro);
 
            if($rsEmpenhoContrato->getNumLinhas() == -1){

                include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
                $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
                $stFiltro  = "   AND e.exercicio    = '".$_REQUEST['stExercicioEmpenho']."'";
                $stFiltro .= "   AND e.cod_entidade =  ".$_REQUEST['inCodEntidade'];
                $stFiltro .= "   AND e.cod_empenho  =  ".$arRequest[0];
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsRecordSet, $stFiltro);
 
                if( $rsRecordSet->getNumLinhas() > 0 ){
                    if( !SistemaLegado::comparaDatas($_REQUEST['dtInicial'],$rsRecordSet->getCampo('dt_empenho') )){
                        if( count( $arEmpenhos ) > 0 ){
                            foreach( $arEmpenhos as $key => $array ){
                                $stCod = $array['cod_empenho'];
                                $stEnt = $array['cod_entidade'];
        
                                if( $arRequest[0] == $stCod and $_REQUEST['inCodEntidade'] == $stEnt ){
                                    $boIncluir = false;
                                    $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                                    break;
                                }
                            }
                        }
                        if( $boIncluir ){     
                            $arRegistro['cod_entidade'] = $rsRecordSet->getCampo('cod_entidade'	);
                            $arRegistro['cod_empenho' ] = $rsRecordSet->getCampo('cod_empenho'	);
                            $arRegistro['data_empenho'] = $rsRecordSet->getCampo('dt_empenho'	);
                            $arRegistro['nom_cgm'     ] = $rsRecordSet->getCampo('credor'	);
                            $arRegistro['exercicio'   ] = $rsRecordSet->getCampo('exercicio'	);
                            $arEmpenhos[] = $arRegistro ;
                                     
                            Sessao::write('arEmpenhos', $arEmpenhos);
                            $stJs .= "f.inCodEntidade.disabled = true; ";
                            $stJs .= "f.stNomEntidade.disabled = true; ";
                            $stJs .= "f.btnLimparEmp.disabled  = true; ";
                            $stJs .= "f.btnIncluirEmp.disabled = true; "; 
                            $stJs .= "f.cod_entidade.value = ".$_REQUEST['inCodEntidade']."; ";
                            $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                            $stJs .= "f.stEmpenho.value = '';";
                            $stJs .= "f.numEmpenho.value = '';";
                            $stJs .= "f.numEmpenho.focus();";
                            $stJs .= montaListaEmpenhos();
                        }
                    }else{
                        $stJs .= "alertaAviso('Início do período do contrato posterior a data do empenho.','form','erro','".Sessao::getId()."');";
                    }
                }else{
                    $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                    $stJs .= "f.stEmpenho.value = '';";
                    $stJs .= "f.numEmpenho.value = '';";
                    $stJs .= "f.numEmpenho.focus();";
                    $stJs .= "alertaAviso('Empenho informado inválido.','form','erro','".Sessao::getId()."');";
                }
            }else{
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.stEmpenho.value = '';";
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.numEmpenho.focus();";
                $stJs .= "alertaAviso('Empenho já vinculado a um contrato.','form','erro','".Sessao::getId()."');";
            }
        }else{
            $stJs .= "alertaAviso('Informe o código de empenho e exercício.','form','erro','".Sessao::getId()."');";
        }
        echo $stJs;        
    break;

    case "excluirEmpenhoLista": 
        $arTempEmp = array();
        $arEmpenhos = Sessao::read('arEmpenhos');
        $arAditivo = Sessao::read('arAditivo');
        $stJs = "";
        
        for($i=0;$i<count($arAditivo);$i++){
            if(isset($arAditivo[$i]['itens'])){
                for($a=0;$a<count($arAditivo[$i]['itens']);$a++){
                    if($arAditivo[$i]['itens'][$a]['cod_empenho'].$arAditivo[$i]['itens'][$a]['cod_entidade'].$arAditivo[$i]['itens'][$a]['exercicio']==$_REQUEST['codEmpenho'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio']){
                        $stJs = "alertaAviso('Empenho vinculado há um aditivo.','form','erro','".Sessao::getId()."');";
                        $Erro=true;
                    }
                }
            }
        }
        
        if(!$Erro){
            foreach ( $arEmpenhos as $registro ) {
                if ( $registro['cod_empenho'].$registro['cod_entidade'].$registro['exercicio'] != $_REQUEST['codEmpenho'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio']  ) {
                    $arTempEmp[] = $registro;
                }
            }

            if(count($arTempEmp) == 0 && $_REQUEST['stAcao']!='alterar'){
                $stJs .= "f.inCodEntidade.disabled = false; ";
                $stJs .= "f.stNomEntidade.disabled = false; ";
            }
            
            $stJs .= "f.btnIncluirEmp.disabled = false; ";
            
            Sessao::write('arEmpenhos', $arTempEmp);
            $stJs .= montaListaEmpenhos();
        }

        echo $stJs;
    break;
    
    case "excluirFornecedorLista":
        $arTempEmp = array();
        $arFornecedores = Sessao::read('arFornecedores');

        foreach ( $arFornecedores as $registro ) {
            if (  $registro['inCodFornecedor'] != $_REQUEST['codFornecedor'] ) {
                $arTempEmp[] = $registro;
            }
        }

        Sessao::write('arFornecedores', $arTempEmp);
        $stJs .= MontaFornecedores();

        echo $stJs;        
    break;

    case "limparFornecedor":
        $stJs .= 'd.getElementById("inCodFornecedor").innerHTML = "";';
        $stJs .= 'd.getElementById("inCodFornecedor").value = "";';
        $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stNomCGM").value = "";';
        $stJs .= 'd.getElementById("cgmRepLegal").innerHTML = "";';
        $stJs .= 'd.getElementById("cgmRepLegal").value = "";';
        $stJs .= 'd.getElementById("stNomRepLegal").innerHTML = "&nbsp;";';
        $stJs .= 'd.getElementById("stNomRepLegal").value = "";';
        
        echo $stJs;   
    break;

    case "limpar":
             $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
             $stJs .= "f.numEmpenho.value = '';";
             $stJs .= "f.numEmpenho.focus();";
             $stJs .= "f.btnLimparEmp.disabled = true; ";
             $stJs .= "f.btnIncluirEmp.disabled = true; ";   

        echo $stJs;
    break;

    case "comparaData":
        $arData = array();        
        $arData = explode('/',$_REQUEST['dtInicial']);

        if($arData[2] !=  Sessao::getExercicio()){
            $stJs  = "f.dtInicial.value = '';";
            $stJs .= "f.dtInicial.focus();\n";
            $stJs .= "alertaAviso('Data Inicial do contrato deve estar no mesmo período do exercício.','form','erro','".Sessao::getId()."');\n";
        }

        if($_REQUEST['dtInicial'] != "" and $_REQUEST['dtFinal'] != ""){
            if( SistemaLegado::comparaDatas($_REQUEST['dtInicial'],$_REQUEST['dtFinal']) ){
                $stJs  = "f.dtFinal.value = '';";
                $stJs .= "f.dtFinal.focus();\n";
                $stJs .= "alertaAviso('Data Final do contrato anterior a Data Inicial.','form','erro','".Sessao::getId()."');\n";
            }else{
                if( $_REQUEST['dtPublicacao'] != "" ){
                    if(SistemaLegado::comparaDatas($_REQUEST['dtPublicacao'],$_REQUEST['dtInicial'])){
                        $stJs  = "f.dtPublicacao.value = '';";
                        $stJs .= "f.dtPublicacao.focus();\n";
                        $stJs .= "alertaAviso('Data de Publicação posterior a Data Inicial do contrato.','form','erro','".Sessao::getId()."');\n";
                    }else{
                        $stJs  = "f.btnLimparEmp.disabled = false; ";
                        $stJs .= "f.btnIncluirEmp.disabled = false; ";
                    }
                }
            }
        }elseif( $_REQUEST['dtInicial'] == "" ){
            $stJs  = "f.btnLimparEmp.disabled = true; ";
            $stJs .= "f.btnIncluirEmp.disabled = true; ";
        }

        echo $stJs;
    break;

    case "preencheInner":
        if($_REQUEST['inCodEntidade'] and $_REQUEST['stExercicioEmpenho'] and $_REQUEST['numEmpenho']){
            include_once( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php' );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $stFiltro  = "   AND e.exercicio    = '".$_REQUEST['stExercicioEmpenho']."'";
            $stFiltro .= "   AND e.cod_entidade =  ".$_REQUEST['inCodEntidade'];
            $stFiltro .= "   AND e.cod_empenho  =  ".$_REQUEST['numEmpenho'];
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsRecordSet, $stFiltro);

            if($rsRecordSet->getNumLinhas() > 0){    
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "'.$rsRecordSet->getCampo('credor').'";';
                $stJs .= "if(f.dtInicial.value!=''){";
                $stJs .= "f.btnLimparEmp.disabled = false; ";
                $stJs .= "f.btnIncluirEmp.disabled = false; ";
                $stJs .= "}";
            }else{
                $stJs  = "alertaAviso('Empenho inexistente.','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.numEmpenho.focus();\n";
            }
        }else{
            if(!$_REQUEST['inCodEntidade']){
                $stJs  = "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inCodEntidade.focus();\n";
                $stJs .= "f.numEmpenho.value = '';";
            }
            if(!$_REQUEST['stExercicioEmpenho']){
                $stJs  = "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.stExercicioEmpenho.focus();\n";
                $stJs .= "f.numEmpenho.value = '';";
            }
            if(!$_REQUEST['numEmpenho']){
                $stJs  = "alertaAviso('Informe o número do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.numEmpenho.focus();\n";
                $stJs .= "d.getElementById('stEmpenho').innerHTML = '&nbsp;';";
            }  
        }

        echo $stJs;
    break;


    case "MontaUnidade":
	$stJs = "";
        if( $_REQUEST["inCodOrgao"] ){
            $stJs .= MontaUnidade($_REQUEST["inCodOrgao"]);
        }

    	echo $stJs;
    break;
    
    case "MontaUnidadeModalidade":
	$stJs = "";
        if( $_REQUEST["inCodOrgaoModalidade"] ){
            $stJs .= MontaUnidadeModalidade($_REQUEST["inCodOrgaoModalidade"]);
        }

	echo $stJs;
    break;
    
    case "MontaModalidade":
        $stJs = MontaModalidade($_REQUEST["cod_modalidade"]);
        
        echo $stJs;
    break;
    
    case "MontaFormaNatureza":
        if(isset($_REQUEST["cod_objeto"]) && ($_REQUEST["cod_objeto"]>=1 && $_REQUEST["cod_objeto"]<4)){
            $stJs = MontaFormaNatureza();
        }else{
            $stJs= "jQuery('#spnFormaNatureza').html('');";
        }
        
        echo $stJs;
    break;
    
    case "incluirFornecedorLista":
        $stJs="";
        $arFornecedores = Sessao::read('arFornecedores');

        if(isset($_REQUEST["inCodFornecedor"]) && $_REQUEST["inCodFornecedor"]>=1 && isset($_REQUEST["stNomCGM"]) && $_REQUEST["stNomCGM"]!='' && isset($_REQUEST["cgmRepLegal"]) && $_REQUEST["cgmRepLegal"]!=''){
            $arRegistro['inCodFornecedor']  = $_REQUEST["inCodFornecedor"];
            $arRegistro['stNomCGM']         = $_REQUEST["stNomCGM"];
            $arRegistro['cgmRepLegal']      = $_REQUEST["cgmRepLegal"];
            $arRegistro['stNomRepLegal']    = $_REQUEST["stNomRepLegal"];
            
            $stJs .= 'd.getElementById("inCodFornecedor").innerHTML = "";';
            $stJs .= 'd.getElementById("inCodFornecedor").value = "";';
            $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stNomCGM").value = "";';
            $stJs .= 'd.getElementById("cgmRepLegal").innerHTML = "";';
            $stJs .= 'd.getElementById("cgmRepLegal").value = "";';
            $stJs .= 'd.getElementById("stNomRepLegal").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("stNomRepLegal").value = "";';
            
            if($arFornecedores!=''){
                foreach( $arFornecedores as $key => $array ){
                    $inCodFornecedor = $array['inCodFornecedor'];
                    $stNomCGM = $array['stNomCGM'];
    
                    if( $arRegistro['inCodFornecedor'] == $inCodFornecedor and $arRegistro['stNomCGM'] == $stNomCGM ){
                        $stJs .= "alertaAviso('Fornecedor já incluso na lista.','form','erro','".Sessao::getId()."');";
                        $erro=true;
                        break;
                    }
                }
            }
            
            if(!$erro){
                $arFornecedores[] = $arRegistro ;
            }
            
            Sessao::write('arFornecedores', $arFornecedores);
            
            $stJs .= MontaFornecedores();
        }
        else{
            $mensagem = "";
            if(!isset($_REQUEST["inCodFornecedor"]) || $_REQUEST["inCodFornecedor"]==''|| $_REQUEST["inCodFornecedor"]<1){
                $mensagem .= "@Informe o Fornecedor."; 
            }
            if(!isset($_REQUEST["cgmRepLegal"]) || $_REQUEST["cgmRepLegal"]==''|| $_REQUEST["cgmRepLegal"]<1){
                $mensagem .= "@Informe o CGM do Representante Legal.";     
            }
            $stJs .= "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');"; 
        }
        
        echo $stJs;
    break;
}
?>
