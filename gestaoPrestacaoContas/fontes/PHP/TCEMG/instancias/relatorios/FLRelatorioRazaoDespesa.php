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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 06/08/2004
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @ignore
    * $Id: FLRelatorioRazaoDespesa.php 64774 2016-03-30 22:01:05Z michel $
    * Casos de uso: uc-02.01.22
*/

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioRazaoDespesa";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma."Filtro.php";
$pgOcGera   = "OCGera".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GF_ORC_COMPONENTES.'IPopUpDotacaoFiltroClassificacao.class.php';

/* includes da pagina javascript */
include_once $pgJS;

$rsRecordset = $rsOrgao = $rsEntidades = new RecordSet();

$obRegra = new ROrcamentoRelatorioBalanceteDespesa;
$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$arNomFiltro = Sessao::read('filtroNomRelatorio');

$obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
$obROrcamentoProjetoAtividade->setExercicio(Sessao::getExercicio());
$obROrcamentoProjetoAtividade->listarSemMascara( $rsPao );

while (!$rsPao->eof()) {
    $arNomFiltro['pao'][$rsPao->getCampo('num_acao')] = $rsPao->getCampo('nom_pao');
    $rsPao->proximo();
}
$rsPao->setPrimeiroElemento();

//Consulta Orgão
$obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

while (!$rsOrgao->eof()) {
    $arNomFiltro['orgao'][$rsOrgao->getCampo('num_orgao')] = $rsOrgao->getCampo('nom_orgao');
    $rsOrgao->proximo();
}
$rsOrgao->setPrimeiroElemento();

$obRegra->obREntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obRegra->obREntidade->setExercicio          (Sessao::getExercicio());
$obRegra->obREntidade->listarUsuariosEntidade($rsEntidades , " ORDER BY cod_entidade");

while (!$rsEntidades->eof()) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo('cod_entidade')] = $rsEntidades->getCampo('nom_cgm');
    $rsEntidades->proximo();
}
Sessao::write('filtroNomRelatorio', $arNomFiltro);
$rsEntidades->setPrimeiroElemento();

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GPC_TCEMG_RELATORIOS.$pgOcul );

//PERIODICIDADE
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio(true);
$obPeriodicidade->setNull(false);
$obPeriodicidade->setValue(4);

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades que deseja pesquisar." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

$arTipoRelatorio = array(
                    '' => 'Selecione', # Campo inicial Selecione
                    'educacao_despesa_extra_orcamentaria' => 'Educação Despesa Extra Orçamentária',
                    'fundeb_60' => 'Fundeb 60%',
                    'fundeb_40' => 'Fundeb 40%',
                    'ensino_fundamental' => 'Ensino Fundamental',
                    'gasto_25' => 'Gasto 25%',
                    'saude' => 'Saúde',
                    'diversos' => 'Diversos',
                    'restos_pagar' => 'Restos a Pagar',
);

$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo ( "Tipo de Relatório" );
$obCmbTipoRelatorio->setName   ( "stTipoRelatorio"   );
$obCmbTipoRelatorio->setId     ( "stTipoRelatorio"   );
$obCmbTipoRelatorio->setStyle  ( "width: 310px"      );
$obCmbTipoRelatorio->addOption ( "", "Selecione"     );
$obCmbTipoRelatorio->setOptions( $arTipoRelatorio    );
$obCmbTipoRelatorio->setNull   ( false               );

//ORGÃO
$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo   ("Órgão");
$obTxtOrgao->setTitle    ("Selecione o órgão orçamentário que deseja pesquisar.");
$obTxtOrgao->setName     ("inNumOrgaoTxt");
$obTxtOrgao->setValue    ($inNumOrgaoTxt);
$obTxtOrgao->setSize     (10);
$obTxtOrgao->setMaxLength(10);
$obTxtOrgao->setInteiro  (true);
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo    ("Órgão");
$obCmbOrgao->setName      ("inNumOrgao");
$obCmbOrgao->setValue     ($inNumOrgao);
$obCmbOrgao->setStyle     ("width: 200px");
$obCmbOrgao->setCampoID   ("num_orgao");
$obCmbOrgao->setCampoDesc ("nom_orgao");
$obCmbOrgao->addOption    ("", "Selecione");
$obCmbOrgao->preencheCombo($rsOrgao);
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );

//UNIDADE
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo   ("Unidade");
$obTxtUnidade->setTitle    ("Selecione a unidade orçamentária que deseja pesquisar." );
$obTxtUnidade->setName     ("inNumUnidadeTxt");
$obTxtUnidade->setValue    ($inNumUnidadeTxt);
$obTxtUnidade->setSize     (10);
$obTxtUnidade->setMaxLength(10);
$obTxtUnidade->setInteiro  (true);

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo   ("Unidade");
$obCmbUnidade->setName     ("inNumUnidade");
$obCmbUnidade->setValue    ($inNumUnidade);
$obCmbUnidade->setStyle    ("width: 200px");
$obCmbUnidade->setCampoID  ("num_unidade");
$obCmbUnidade->setCampoDesc("descricao");
$obCmbUnidade->addOption   ("", "Selecione");

$obTxtPao = new TextBox;
$obTxtPao->setRotulo   ("Ação");
$obTxtPao->setTitle    ("Selecione o Ação para filtro." );
$obTxtPao->setName     ("inCodPaoTxt");
$obTxtPao->setValue    ($inCodPao);
$obTxtPao->setSize     (6);
$obTxtPao->setMaxLength(4);
$obTxtPao->setInteiro  (true);

$obCmbPao = new Select;
$obCmbPao->setRotulo              ( "Ação"          );
$obCmbPao->setName                ( "inCodPao"      );
$obCmbPao->setValue               ( $inCodPao       );
$obCmbPao->setStyle               ( "width: 232px"  );
$obCmbPao->setCampoID             ( "num_acao"      );
$obCmbPao->setCampoDesc           ( "nom_pao"       );
$obCmbPao->addOption              ( "", "Selecione" );
$obCmbPao->preencheCombo          ( $rsPao          );


$rsRecursos = $rsRecordsetRecursos = new Recordset;
$obTOrcamentoRecurso = new TOrcamentoRecurso;
$obTOrcamentoRecurso->recuperaRecursoExercicio($rsRecursos, '', 'ORDER BY recurso.cod_recurso');

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbRecursos = new SelectMultiplo();
$obCmbRecursos->setName   ('inCodRecurso');
$obCmbRecursos->setRotulo ( "Recursos" );
$obCmbRecursos->setTitle  ( "Selecione os recursos que deseja pesquisar." );
$obCmbRecursos->setNull   ( false );


// lista de recursos disponiveis
$obCmbRecursos->SetNomeLista1 ('inCodRecursoDisponivel');
$obCmbRecursos->setCampoId1   ( 'cod_recurso' );
$obCmbRecursos->setCampoDesc1 ('[cod_recurso] - [nom_recurso]');
$obCmbRecursos->SetRecord1    ( $rsRecursos );

// lista de recursos selecionados
$obCmbRecursos->SetNomeLista2 ('inCodRecurso');
$obCmbRecursos->setCampoId2   ('cod_recurso');
$obCmbRecursos->setCampoDesc2 ('[cod_recurso] - [nom_recurso]');
$obCmbRecursos->SetRecord2    ( $rsRecordsetRecursos );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                     );
$obCmbSituacao->setName                ( "inSituacao"                   );
$obCmbSituacao->setStyle               ( "width: 200px"                 );
$obCmbSituacao->addOption              ( "", "Selecione"                );
$obCmbSituacao->addOption              ( "2", "Pagos"                   );
$obCmbSituacao->setNull                ( false );

//Define objeto span para componentes de filtro
$obSpan = new Span;
$obSpan->setId( "spnFormularioFiltro" );

$obIPopUpDotacao = new IPopUpDotacaoFiltroClassificacao($obCmbEntidades);
$obIPopUpDotacao->obCampoCod->setName('inCodDespesa');
$obIPopUpDotacao->obCampoCod->setId  ('inCodDespesa');
$obIPopUpDotacao->setNull            (true);
$obIPopUpDotacao->setId              ('stNomDespesa');
$obImagem = $obIPopUpDotacao->getImagem();
$obImagem->setId('inImgDespesa');
$obIPopUpDotacao->setImagem ($obImagem);

$obFormularioFiltro = new Formulario;
$obFormularioFiltro->addComponenteComposto($obTxtOrgao  , $obCmbOrgao  );
$obFormularioFiltro->addComponenteComposto($obTxtUnidade, $obCmbUnidade);
$obFormularioFiltro->addComponenteComposto($obTxtPao    , $obCmbPao    );

$obFormularioFiltro->montaInnerHTML();
$stHtml = $obFormularioFiltro->getHTML();

$jsOnLoad = "
    function salvar() {
        selecionaTodosSelect(document.frm.inCodEntidade);
        selecionaTodosSelect(document.frm.inCodRecurso);
        document.frm.submit();
    }
  
    jQuery('#spnFormularioFiltro').hide();
    jQuery('#stTipoRelatorio').change(function(){

        //Apresenta filtro para seleção do órgão, unidade e ação
        if(    jQuery(this).val() == 'fundeb_40'
           || jQuery(this).val() == 'fundeb_60'
           || jQuery(this).val() == 'ensino_fundamental'
           || jQuery(this).val() == 'gasto_25'
           || jQuery(this).val() == 'saude'
           || jQuery(this).val() == 'diversos' ){
            
           jQuery('#spnFormularioFiltro').html('".$stHtml."');
           jQuery('#spnFormularioFiltro').show();

           jQuery('#inCodDespesa').attr('disabled', false);
           jQuery('#inImgDespesa').attr('hidden', false);
        }
        
        //Apresenta caixa para seleção dos recursos
        if(   jQuery(this).val() == 'educacao_despesa_extra_orcamentaria'
           || jQuery(this).val() == 'restos_pagar' ){
           
           jQuery('#spnFormularioFiltro').html('".$stHtmlRecurso."');
           jQuery('#spnFormularioFiltro').show();

           jQuery('#inCodDespesa').val('');
           jQuery('#stNomDespesa').html('&nbsp;');
           jQuery('#inCodDespesa').attr('disabled', true);
           jQuery('#inImgDespesa').attr('hidden', true);
        }
    });
";

$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-02.01.22'      );
$obFormulario->addForm      ($obForm            );
$obFormulario->addHidden    ($obHdnCaminho      ); 
$obFormulario->addTitulo    ("Dados para Filtro");
$obFormulario->addComponente($obCmbEntidades    );
$obFormulario->addComponente($obPeriodicidade   );
$obFormulario->addComponente($obCmbTipoRelatorio);
$obFormulario->addSpan      ($obSpan            );
$obFormulario->addComponente($obCmbSituacao     );
$obFormulario->addComponente($obCmbRecursos     );
$obFormulario->addComponente($obIPopUpDotacao   );

// BOTÕES DE AÇÃO DO FORMULÁRIO (OK/LIMPAR)
$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick('salvar();');
// Botão Limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setName ('btnLimpar');
$obBtnLimpar->setValue('Limpar');

$arBtnForm   = array();
$arBtnForm[] = $obBtnOk;
$arBtnForm[] = $obBtnLimpar;

$obFormulario->defineBarra($arBtnForm);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';