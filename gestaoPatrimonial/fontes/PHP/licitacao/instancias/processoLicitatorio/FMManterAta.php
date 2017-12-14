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
 * Formulário do cadastro de Ata.
 * Data de Criação: 14/01/2009
 *
 *
 * @author Analista:      Gelson Wolowski Gonçalvez <gelson.goncalves@cnm.org.br>
 * @author Desenvolvedor: Diogo Zarpelon            <diogo.zarpelon@cnm.org.br>
 *

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php";
include_once TLIC."TLicitacaoAta.class.php";
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once TLIC."TLicitacaoPublicacaoAta.class.php";
include_once TLIC."TTipoAdesaoAta.class.php";

# Definição dos nomes dos arquivos relacionados ao programa.
$stPrograma = "ManterAta";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$obTLicitacaoAta = new TLicitacaoAta;

include_once ($pgJS);

$stCtrl  = $_REQUEST['stCtrl'];
$stAcao  = $_REQUEST['stAcao'];

$inIdAta  = $_REQUEST['inIdAta'];
$inNumAta = $_REQUEST['inNumAta'];

$rsLicitacaoAta = new RecordSet;
$stExercicioAta = Sessao::getExercicio();

$bloqueado = true;

# Na ação de alterar, preenche os campos com seus respectivos valores.
if ($stAcao == 'alterar') {
    $obTLicitacaoAta->setDado('id' , $inIdAta);
    $obTLicitacaoAta->recuperaAta($rsLicitacaoAta);
    
    $stExercicioAta = $rsLicitacaoAta->getCampo('exercicio_ata');

    # Formata a data.
    $dtDataAta = $rsLicitacaoAta->getCampo('date');
    
    # Formata o horário.
    $timestamp = substr($rsLicitacaoAta->getCampo('timestamp'),11,16);
    list($hora, $minuto) = explode(':', $timestamp);
    $stHoraAta = $hora.':'.$minuto;
    
    # Formata a data de validade.
    $dtDataValidadeAta = $rsLicitacaoAta->getCampo('date_valida');
    
    # Formata o número do edital.
    $stNumEdital = $rsLicitacaoAta->getCampo('num_edital').'/'.$rsLicitacaoAta->getCampo('exercicio');
    
    $inTipoAdesao = $rsLicitacaoAta->getCampo('tipo_adesao');

    $bloqueado = false;
    
    //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
    $obTLicitacaoPublicacaoAta = new TLicitacaoPublicacaoAta();
    $obTLicitacaoPublicacaoAta->setDado('ata_id', $inIdAta );
    $obTLicitacaoPublicacaoAta->setDado('timestamp', $rsLicitacaoAta->getCampo('timestamp'));
    $obTLicitacaoPublicacaoAta->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );
    
    $inCount = 0;
    $arValores = array();
    while ( !$rsVeiculosPublicacao->eof() ) {
        $arValores[$inCount]['id'            ] = $rsVeiculosPublicacao->getCampo( 'id' );
        $arValores[$inCount]['ata_id'     ] = $rsVeiculosPublicacao->getCampo( 'ata_id' );
        $arValores[$inCount]['inVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
        $arValores[$inCount]['stVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'nom_veiculo');
        $arValores[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao');
        $arValores[$inCount]['inNumPublicacao'] = $rsVeiculosPublicacao->getCampo( 'num_publicacao');
        $arValores[$inCount]['stObservacao'  ] = $rsVeiculosPublicacao->getCampo( 'observacao');
        $inCount++;
        $rsVeiculosPublicacao->proximo();
    }
    Sessao::write('arValores', $arValores);
}

$obTTipoAdesaoAta = new TTipoAdesaoAta;
$obTTipoAdesaoAta->recuperaTodos($rsTipoAdesaoAta, " WHERE codigo <> 0");

# Componentes do formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setValue($stCtrl);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

# Guarda o ID da Ata.
$obHdnIdAta = new Hidden;
$obHdnIdAta->setName ("inIdAta");
$obHdnIdAta->setValue($rsLicitacaoAta->getCampo('id'));

//Define o objeto de controle do id na listagem do veiculo de publicação
$obHdnCodVeiculo= new Hidden;
$obHdnCodVeiculo->setName  ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setId ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setValue ( ""           );

$obNumAta = new Inteiro;
$obNumAta->setTitle ('Número da Ata');
$obNumAta->setName  ('inNumAta');
$obNumAta->setId    ('inNumAta');
$obNumAta->setRotulo('Número da Ata');
$obNumAta->setTitle ('Informe o número da Ata.');
$obNumAta->setSize  (10);
$obNumAta->setNull  (false);
$obNumAta->setValue ($rsLicitacaoAta->getCampo('num_ata'));
$obNumAta->obEvento->setOnBlur("montaParametrosGET('validaNumAta','inIdAta','inNumAta');");

$obLabelExercicio = new Label;
$obLabelExercicio->setValue('/'.$stExercicioAta);

$stFiltroBuscaEditais = " AND (                                                                                                             \n
                                EXISTS  (                                                                                                   \n
                                          SELECT 1                                                                                          \n
                                            FROM compras.julgamento                                                                         \n
                                      INNER JOIN compras.mapa_cotacao                                                                       \n
                                              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao                                      \n
                                             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao                                          \n

                                           WHERE ll.cod_licitacao = le.cod_licitacao                                                        \n
                                             AND ll.cod_modalidade = le.cod_modalidade                                                      \n
                                             AND ll.cod_entidade = le.cod_entidade                                                          \n
                                             AND ll.exercicio = le.exercicio                                                                \n
                                             AND ll.exercicio_mapa = mapa_cotacao.exercicio_mapa                                            \n
                                             AND ll.cod_mapa = mapa_cotacao.cod_mapa                                                        \n
                                             AND NOT EXISTS (                                                                               \n
                                                               SELECT 1                                                                     \n
                                                                 FROM compras.cotacao_anulada                                               \n
                                                                WHERE cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao                \n
                                                                  AND cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao            \n
                                                            )                                                                               \n
                                        )                                                                                                   \n
                            )                                                                                                               \n
                        AND NOT EXISTS (SELECT *                                                                                            \n
                                          FROM licitacao.ata                                                                                \n
                                         WHERE ata.num_edital = le.num_edital                                                               \n
                                           AND ata.exercicio  = le.exercicio                                                                \n
                                        ) ";

# Pesquisa pelo número do edital.
$obPopUpNumeroEdital = new IPopUpNumeroEdital($obForm);
$obPopUpNumeroEdital->obCampoCod->setId  ('stNumEdital');
$obPopUpNumeroEdital->obCampoCod->setName('stNumEdital');
$obPopUpNumeroEdital->setNull(false);
$obPopUpNumeroEdital->setValidacaoPadraoEdital(false);
$obPopUpNumeroEdital->obCampoCod->setSize(10);
$obPopUpNumeroEdital->obCampoCod->obEvento->setOnChange($obPopUpNumeroEdital->obCampoCod->obEvento->getOnChange()." if (jQuery(this).val() != '') { montaParametrosGET('validaEdital', ''); } else { jQuery('#btnSugerir').attr('disabled','disabled');}");
$obPopUpNumeroEdital->obCampoCod->obEvento->setOnBlur("if (jQuery(this).val() != '') { jQuery('#btnSugerir').removeAttr('disabled'); } else { jQuery('#btnSugerir').attr('disabled','disabled');jQuery('#stDescricaoAta').val('');}");
$obPopUpNumeroEdital->obCampoCod->obEvento->setOnFocus("if (jQuery(this).val() != '') { jQuery('#btnSugerir').removeAttr('disabled'); } else { jQuery('#btnSugerir').attr('disabled','disabled');jQuery('#stDescricaoAta').val('');}");
$obPopUpNumeroEdital->obCampoCod->setValue($stNumEdital);
$obPopUpNumeroEdital->setFiltroEditaisAdicional($stFiltroBuscaEditais);

$obDataEntrega = new Data;
$obDataEntrega->setName  ('dtDataAta' );
$obDataEntrega->setId    ('dtDataAta' );
$obDataEntrega->setRotulo('Data da Ata' );
$obDataEntrega->setTitle ('Informe a data da Ata.' );
$obDataEntrega->setValue ($dtDataAta);
$obDataEntrega->setNull  (false);

$obHoraAta = new Hora;
$obHoraAta->setName  ('stHoraAta');
$obHoraAta->setId  ('stHoraAta');
$obHoraAta->setRotulo('Hora da Ata');
$obHoraAta->setTitle ('Informe a hora da Ata.');
$obHoraAta->setValue ($stHoraAta);
$obHoraAta->setNull  (false);
$obHoraAta->setSize  (10);

$obDataValidade = new Data;
$obDataValidade->setName  ('dtDataValidadeAta' );
$obDataValidade->setId    ('dtDataValidadeAta' );
$obDataValidade->setRotulo('Data da Validade da Ata' );
$obDataValidade->setTitle ('Informe a data da Validade da Ata.' );
$obDataValidade->setValue ($dtDataValidadeAta);
$obDataValidade->setNull  (false);

$obSelectTipo = new Select;
$obSelectTipo->setName       ('cmbTipoAdesao');
$obSelectTipo->setId         ('cmbTipoAdesao');
$obSelectTipo->setRotulo     ('Tipo de Adesão');
$obSelectTipo->setTitle      ('Informe o Tipo de Adesão');
$obSelectTipo->addOption     ('', 'Selecione');
$obSelectTipo->setCampoId    ('codigo');
$obSelectTipo->setCampoDesc  ('descricao');
$obSelectTipo->setValue      ($inTipoAdesao);
$obSelectTipo->setNull       (false);
$obSelectTipo->preencheCombo ($rsTipoAdesaoAta);

$obButtonSugestao = new Button;
$obButtonSugestao->setTitle ('Clique no botão para gerar uma sugestão com os dados da Ata.');
$obButtonSugestao->setRotulo('Sugerir Informações');
$obButtonSugestao->setId    ('btnSugerir');
$obButtonSugestao->setValue ('Sugerir');
$obButtonSugestao->setDisabled($bloqueado);
//$obButtonSugestao->obEvento->setOnClick("montaParametrosGET('sugerirDados', 'dtDataAta, stHoraAta, stNumEdital')");
$obButtonSugestao->obEvento->setOnClick("abrePopUpSugestao('" . CAM_GP_LIC_POPUPS . "processoLicitatorio/FLApresentaSugestaoAta.php?".Sessao::getId()."','frm','','','','".Sessao::getId()."','800','550');");

$obDescricaoAta = new TextArea;
$obDescricaoAta->setName  ('stDescricaoAta');
$obDescricaoAta->setId    ('stDescricaoAta');
$obDescricaoAta->setRotulo('Descrição' );
$obDescricaoAta->setTitle ('Preencha a descrição da Ata.' );
$obDescricaoAta->setValue ($rsLicitacaoAta->getCampo('descricao'));
$obDescricaoAta->setNull  (false);
$obDescricaoAta->setRows  (30);
$obDescricaoAta->setStyle ('width:95%!important;');

//Painel veiculos de publicidade
$obVeiculoPublicidade = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidade->setTabelaVinculo       ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicidade->setCampoVinculo        ( 'numcgm'                         );
$obVeiculoPublicidade->setNomeVinculo         ( 'Veículo de Publicação'          );
$obVeiculoPublicidade->setRotulo              ( '*Veículo de Publicação'         );
$obVeiculoPublicidade->setTitle               ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidade->setName                ( 'stNomCgmVeiculoPublicacao'     );
$obVeiculoPublicidade->setId                  ( 'stNomCgmVeiculoPublicacao'     );
$obVeiculoPublicidade->obCampoCod->setName    ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setId      ( 'inVeiculo'                      );
$obVeiculoPublicidade->setNull( true );
$obVeiculoPublicidade->obCampoCod->setNull( true );

$obDataPublicacao = new Data();
$obDataPublicacao->setId   ( "dtDataPublicacao" );
$obDataPublicacao->setName ( "dtDataPublicacao" );
$obDataPublicacao->setValue( date('d/m/Y') );
$obDataPublicacao->setRotulo( "Data de Publicação" );
$obDataPublicacao->setObrigatorioBarra( true );
$obDataPublicacao->setTitle( "Informe a data de publicação." );

$obNumeroPublicacao = new Inteiro();
$obNumeroPublicacao->setId   ( "inNumPublicacao" );
$obNumeroPublicacao->setName ( "inNumPublicacao" );
$obNumeroPublicacao->setValue( "");
$obNumeroPublicacao->setRotulo( "Número Publicação" );
$obNumeroPublicacao->setObrigatorioBarra( false );
$obNumeroPublicacao->setTitle( "Informe o Número da Publicação." );

//Campo Observação da Publicação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId     ( "stObservacao"                               );
$obTxtObservacao->setName   ( "stObservacao"                               );
$obTxtObservacao->setValue  ( ""                                           );
$obTxtObservacao->setRotulo ( "Observação"                                 );
$obTxtObservacao->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacao->setObrigatorioBarra( false                               );
$obTxtObservacao->setRows   ( 2                                            );
$obTxtObservacao->setCols   ( 100                                          );
$obTxtObservacao->setMaxCaracteres( 80 );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculo = new Button;
$obBtnIncluirVeiculo->setValue             ( "Incluir"                                      );
$obBtnIncluirVeiculo->setId                ( "incluiVeiculo"                                );
$obBtnIncluirVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculos', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicacao, stObservacao, inNumAta');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "montaParametrosGET('limparVeiculo', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicacao, stObservacao, inNumAta');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

$jsOnLoad = "";
if ($stAcao == 'alterar') {
    $jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaVeiculos');";
}

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

# Hiddens
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnIdAta);
$obFormulario->addHidden($obHdnCodVeiculo);

# Titulo
$obFormulario->addTitulo( 'Dados da Ata' );

# Componentes
$obFormulario->agrupaComponentes(array($obNumAta, $obLabelExercicio, ));
$obFormulario->addComponente($obDataEntrega);
$obFormulario->addComponente($obHoraAta);
$obFormulario->addComponente($obDataValidade);
$obFormulario->addComponente($obSelectTipo);
$obFormulario->addComponente($obPopUpNumeroEdital);
$obFormulario->addComponente($obButtonSugestao);
$obFormulario->addComponente($obDescricaoAta);

$obFormulario->addTitulo        ( 'Veículo de Publicação' );
$obFormulario->addComponente    ( $obVeiculoPublicidade );
$obFormulario->addComponente    ( $obDataPublicacao );
$obFormulario->addComponente    ( $obNumeroPublicacao );
$obFormulario->addComponente    ( $obTxtObservacao );
$obFormulario->defineBarra      ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan          ( $obSpnListaVeiculo );

if ($stAcao != 'alterar') {
    # Botões de controle
    $obFormulario->Ok();
} else {
    $obFormulario->Cancelar($pgList);
}

# Exibe formulário
$obFormulario->Show();

    // Mudando estilo do pop-up de light box
    $css = "<style type='text/css'>
                div#containerPopUp h3{
                    !important;
                     background-color:#323232;
                     color:#fff;
                     padding:3px;
                     margin:-8px 2px 0px -8px;
                     width:610px;
                }
                div#containerPopUp p{
                    !important;
                    height:200px;
                    width:585px;
                    overflow:auto;
                    font-size:12px;
                    color:#0a5a82;
                    border:2px solid #000;
                    padding:5px;
                    background-color:inherit;
                }
                div#containerPopUp div#showPopUp{
                    !important;
                    width:600px;
                    height:330px;
                    overflow:hidden;
                    top:3,0%;
                    left:50%;
                    margin:-90px 0px 0px -300px;
                    position:fixed;
                    border:2px solid #000;
                    background-color:#ff0;
                    padding:10px;
                    background-color:#e4eae4;
                }
                div#containerPopUp input{
                    !important;
                    width: 100px;
                    position:absolute;
                    bottom:25px;
                    left:50%;
                    font-size:10px;
                    color:#0a5a82;
                    font-weight:bold;
                    background-color:#e4eae4;
                    margin-left:-105px;
                }
            </style>";

    echo $css;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
