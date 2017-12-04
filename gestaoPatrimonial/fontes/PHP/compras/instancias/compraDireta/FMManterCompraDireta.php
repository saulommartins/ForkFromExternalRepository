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
    * Página de Formulário de Compra Direta
    * Data de Criação   : 29/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * Casos de uso : uc-03.04.33

    $Id: FMManterCompraDireta.php 66087 2016-07-18 20:33:50Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once CAM_GP_COM_COMPONENTES."ISelectModalidade.class.php";
require_once CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php";
require_once CAM_GP_COM_COMPONENTES."IPopUpEditObjeto.class.php";
require_once CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php";
require_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";
require_once CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php";
require_once TCOM."TComprasCompraDiretaProcesso.class.php";
include_once CAM_GP_COM_MAPEAMENTO.'TComprasConfiguracao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCompraDireta";
$pgForm     = "FM".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

/**
 * IMPORTANTE
 * O caso de uso sempre vai manipular compras do exercicio corrente na sessao.
 */
$stAcao = $request->get('stAcao', Sessao::read("stAcao"));

$arCompraDireta = array();
$boAlteraAnula = ( ( $stAcao == "alterar" ) || ( $stAcao == "anular" ) );

$stDtCompraDireta = "";

if ($boAlteraAnula) {
    // validar
    if ($request->get('inCodCompraDireta')) {
        $stFiltro  = " where compra_direta.cod_compra_direta  = ".$request->get('inCodCompraDireta');
        $stFiltro .= "   and compra_direta.cod_entidade       = ".$request->get('inCodEntidade');
        $stFiltro .= "   and compra_direta.cod_modalidade     = ".$request->get('inCodModalidade');
        $stFiltro .= "   and compra_direta.exercicio_entidade = '".Sessao::getExercicio()."'";

        // Ok, buscar informacoes da compra direta
        require_once TCOM."TComprasCompraDireta.class.php";

        $obTCompraDireta = new TComprasCompraDireta();

        // consulta com mesmo filtro montado acima
        $obTCompraDireta->recuperaTodos( $rsCompraDireta , $stFiltro );

        // formata valores e inserir em array
        $arCompraDireta = array();
        $arCompraDireta['cod_compra']         = $rsCompraDireta->getCampo('cod_compra_direta');
        $arCompraDireta['cod_entidade']       = $rsCompraDireta->getCampo('cod_entidade');
        $arCompraDireta['exercicio_entidade'] = $rsCompraDireta->getCampo('exercicio_entidade');
        $arCompraDireta['cod_modalidade']     = $rsCompraDireta->getCampo('cod_modalidade');
        $arCompraDireta['cod_objeto']         = $rsCompraDireta->getCampo('cod_objeto');
        $arCompraDireta['cod_tipo_objeto']    = $rsCompraDireta->getCampo('cod_tipo_objeto');
        $arCompraDireta['exercicio_mapa']     = $rsCompraDireta->getCampo('exercicio_mapa');
        $arCompraDireta['cod_mapa']           = $rsCompraDireta->getCampo('cod_mapa');
        $arCompraDireta['cod_processo']       = $rsCompraDireta->getCampo('cod_processo');

        list($ano, $mes, $dia) = explode("-", substr($rsCompraDireta->getCampo('timestamp'), 0, 10));
        $stDtCompraDireta = $dia."/".$mes."/".$ano;

        $arCompraDireta['obDtCompraDireta'] = $stDtCompraDireta;

        list ( $ano, $mes, $dia ) = explode('-' , $rsCompraDireta->getCampo('dt_entrega_proposta') );
        $arCompraDireta['dt_entrega_proposta']  = $dia.$mes.$ano;

        list ( $ano, $mes, $dia ) = explode('-' , $rsCompraDireta->getCampo('dt_validade_proposta') );
        $arCompraDireta['dt_validade_proposta'] = $dia.$mes.$ano;
        $arCompraDireta['condicoes_pagamento']  = $rsCompraDireta->getCampo('condicoes_pagamento');
        $arCompraDireta['prazo_entrega']        = $rsCompraDireta->getCampo('prazo_entrega');
    }
} else {
    $arCompraDireta = array();
    $arCompraDireta['cod_compra']           = "";
    $arCompraDireta['cod_entidade']         = "";
    $arCompraDireta['exercicio_entidade']   = "";
    $arCompraDireta['cod_modalidade']       = "";
    $arCompraDireta['cod_objeto']           = "";
    $arCompraDireta['cod_tipo_objeto']      = "";
    $arCompraDireta['exercicio_mapa']       = "";
    $arCompraDireta['cod_mapa']             = "";
    $arCompraDireta['cod_processo']         = "";
    $arCompraDireta['obDtCompraDireta']     = "";
    $arCompraDireta['dt_entrega_proposta']  = "";
    $arCompraDireta['dt_validade_proposta'] = "";
    $arCompraDireta['condicoes_pagamento']  = "";
    $arCompraDireta['prazo_entrega']        = "";
}

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

//Define o Hidde de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

// LABELS para alterar e anular
if ($boAlteraAnula) {
    //pegar entidade
    $inNumCgmEntidade = SistemaLegado::pegaDado("numcgm", "orcamento.entidade", "where cod_entidade = ".$arCompraDireta['cod_entidade']);
    $stEntidade       = $arCompraDireta['cod_entidade']." - ".SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$inNumCgmEntidade);

    $obLblEntidade = new Label();
    $obLblEntidade->setRotulo( "Entidade"  );
    $obLblEntidade->setValue ( $stEntidade );

    $stModalidade = SistemaLegado::pegaDado('descricao', 'compras.modalidade', 'where cod_modalidade = '.$arCompraDireta['cod_modalidade']);
    $obLblModalidade = new Label();
    $obLblModalidade->setRotulo( "Modalidade"  );
    $obLblModalidade->setValue ( $stModalidade );

    $obLblCompraDireta= new Label();
    $obLblCompraDireta->setRotulo( "Compra Direta"               );
    $obLblCompraDireta->setValue ( $arCompraDireta['cod_compra'] );

    $obLblTipoObjeto= new Label();
    $obLblTipoObjeto->setRotulo( "Tipo Objeto" );
    $obLblTipoObjeto->setValue ( $arCompraDireta['cod_tipo_objeto'].' - '.SistemaLegado::pegaDado('descricao', 'compras.tipo_objeto', 'where cod_tipo_objeto = '.$arCompraDireta['cod_tipo_objeto']) );

    $obLblObjeto= new Label();
    $obLblObjeto->setRotulo( "Objeto" );
    $obLblObjeto->setName  ( 'lblObjeto' );
    $obLblObjeto->setId    ( 'lblObjeto' );
    $obLblObjeto->setValue ( $arCompraDireta['cod_objeto'].' - '.stripslashes(SistemaLegado::pegaDado('descricao', 'compras.objeto', 'where cod_objeto = '.$arCompraDireta['cod_objeto'])) );

    $obLblDataEntregaProposta= new Label();
    $obLblDataEntregaProposta->setRotulo( "Data de Entrega da Proposta"          );
    $obLblDataEntregaProposta->setValue ( $arCompraDireta['dt_entrega_proposta'] );

    $obLblDataValidadeProposta= new Label();
    $obLblDataValidadeProposta->setRotulo( "Validade da Proposta"                  );
    $obLblDataValidadeProposta->setValue ( $arCompraDireta['dt_validade_proposta'] );

    $obLblCondicoesPagamento= new Label();
    $obLblCondicoesPagamento->setRotulo( "Condições de Pagamento"               );
    $obLblCondicoesPagamento->setValue ( $arCompraDireta['condicoes_pagamento'] );

    $obLblPrazoEntrega= new Label();
    $obLblPrazoEntrega->setRotulo( "Prazo de Entrega"                           );
    $obLblPrazoEntrega->setValue ( $arCompraDireta['prazo_entrega'] . " dia(s)" );

    $obLblMapaCompras= new Label();
    $obLblMapaCompras->setRotulo( "Mapa de Compras"                                                 );
    $obLblMapaCompras->setValue ( $arCompraDireta['cod_mapa']."/".$arCompraDireta['exercicio_mapa'] );

    $stFiltro  = " where compra_direta_processo.cod_compra_direta  = ".$arCompraDireta['cod_compra'];
    $stFiltro .= "   and compra_direta_processo.cod_entidade 	   = ".$arCompraDireta['cod_entidade'];
    $stFiltro .= "   and compra_direta_processo.cod_modalidade     = ".$arCompraDireta['cod_modalidade'];
    $stFiltro .= "   and compra_direta_processo.exercicio_entidade = '".$arCompraDireta['exercicio_entidade']."'";

    $obTComprasCompraDiretaProcesso = new TComprasCompraDiretaProcesso();
    $obTComprasCompraDiretaProcesso->recuperaTodos( $rsProcesso , $stFiltro);

    if ($stAcao == 'alterar') {
        $obPopUpProcesso = new IPopUpProcesso($obForm);
        $obPopUpProcesso->setRotulo("Processo Administrativo");
        $obPopUpProcesso->setValidar(true);
        if( SistemaLegado::pegaConfiguracao('cod_uf',2,Sessao::getExercicio(), $boTransacao) == 2 )
            $obPopUpProcesso->setNull (false);
        
        $stProcesso = explode ("/",$request->get('stProcesso'));
        if ($rsProcesso->getCampo('cod_processo')!=''&&$rsProcesso->getCampo('exercicio_processo')!='') {
            $obPopUpProcesso->obCampoCod->setValue($rsProcesso->getCampo('cod_processo')."/".$rsProcesso->getCampo('exercicio_processo'));
        }
    } else {
        $obLblProcesso = new Label();
        $obLblProcesso->setRotulo( "Processo administrativo" );
        $obLblProcesso->setValue( $rsProcesso->getCampo('cod_processo')."/".$rsProcesso->getCampo('exercicio_processo'));
    }

    // hiddens
    $obHdnEntidade = new Hidden();
    $obHdnEntidade->setId    ( "inCodEntidade"                 );
    $obHdnEntidade->setName  ( "inCodEntidade"                 );
    $obHdnEntidade->setValue ( $arCompraDireta['cod_entidade'] );

    $obHdnModalidade = new Hidden();
    $obHdnModalidade->setId    ( "inCodModalidade"                 );
    $obHdnModalidade->setName  ( "inCodModalidade"                 );
    $obHdnModalidade->setValue ( $arCompraDireta['cod_modalidade'] );

    $obHdnCompraDireta = new Hidden();
    $obHdnCompraDireta->setId    ( "inCodCompraDireta"           );
    $obHdnCompraDireta->setName  ( "inCodCompraDireta"           );
    $obHdnCompraDireta->setValue ( $arCompraDireta['cod_compra'] );

    $obHdnMapaCompras = new Hidden();
    $obHdnMapaCompras->setId    ( 'hdnMapaCompras'                                                  );
    $obHdnMapaCompras->setName  ( 'hdnMapaCompras'                                                  );
    $obHdnMapaCompras->setValue ( $arCompraDireta['cod_mapa'].'/'.$arCompraDireta['exercicio_mapa'] );

    $obHdnIdMapaCompras = new Hidden();
    $obHdnIdMapaCompras->setId    ( 'hdnIdMapaCompras'          );
    $obHdnIdMapaCompras->setName  ( 'hdnIdMapaCompras'          );
    $obHdnIdMapaCompras->setValue ( $arCompraDireta['cod_mapa'] );

    $obHdnExercicioMapaCompras = new Hidden();
    $obHdnExercicioMapaCompras->setId    ( 'hdnExercicioMapaCompras'         );
    $obHdnExercicioMapaCompras->setName  ( 'hdnExercicioMapaCompras'         );
    $obHdnExercicioMapaCompras->setValue ( $arCompraDireta['exercicio_mapa'] );
} else {
    if ($stAcao == 'incluir') {
        $obTConfiguracao = new TComprasConfiguracao;
        $obTConfiguracao->setDado('parametro', 'numeracao_automatica');
        $obTConfiguracao->recuperaPorChave($rsConfiguracao);
        $boIdCompraDiretaAutomatica = $rsConfiguracao->getCampo('valor');

        // Caso o parâmetro não for true, constroi o campo para o usuário informar o cód. da licitação.
        if ($boIdCompraDiretaAutomatica != 't') {
            $obCodCompraDireta = new Inteiro();
            $obCodCompraDireta->setId    ( 'inCodCompraDireta'                  );
            $obCodCompraDireta->setName  ( 'inCodCompraDireta'                  );
            $obCodCompraDireta->setRotulo( 'Compra Direta'                      );
            $obCodCompraDireta->setTitle ( 'Informe o código da compra direta.' );
        }

        $obPopUpProcesso = new IPopUpProcesso($obForm);
        $obPopUpProcesso->setRotulo ( "Processo Administrativo" );
        $obPopUpProcesso->setValidar( true                      );
        $obPopUpProcesso->setValue  ( ""                        );
        if( SistemaLegado::pegaConfiguracao('cod_uf',2,Sessao::getExercicio(), $boTransacao) == 2 )
            $obPopUpProcesso->setNull (false);
        
        $stProcesso = explode ("/",$request->get('stProcesso'));
    }

    $obLblObjeto = new Label();
    $obLblObjeto->setName  ( 'lblObjeto' );
    $obLblObjeto->setId    ( 'lblObjeto' );
    $obLblObjeto->setRotulo( 'Objeto'    );
}

# Requisição para sugerir a data contábil.
$stJsDataContabil  = " if (this.value != '') { ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade='+this.value+'','recuperaUltimaDataContabil'); } else { jQuery('#stDtCompraDireta').val(''); montaParametrosGET('LiberaDataCompra'); }";
$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
if( $stDtCompraDireta == '' ){
    $obEntidadeUsuario->obSelect->obEvento->setOnChange ( $stJsDataContabil );
    $obEntidadeUsuario->obTextBox->obEvento->setOnChange( $stJsDataContabil );
}
$obEntidadeUsuario->setNull ( false );

// Define objeto Data da Compra Direta.
$obDtCompraDireta = new Data;
$obDtCompraDireta->setName   ( "stDtCompraDireta"                  );
$obDtCompraDireta->setId     ( "stDtCompraDireta"                  );
$obDtCompraDireta->setRotulo ( "Data da Compra Direta"             );
$obDtCompraDireta->setTitle  ( 'Informe a data da compra direta.'  );
$obDtCompraDireta->setValue  ( $arCompraDireta['obDtCompraDireta'] );
$obDtCompraDireta->setNull   ( false                               );
$obDtCompraDireta->obEvento->setOnBlur ( $obDtCompraDireta->obEvento->getOnBlur()."montaParametrosGET('validaDtCompraDireta','stDtCompraDireta, inCodEntidade, stMapaCompras');" );
$obDtCompraDireta->setLabel  ( true                                );
if( $stDtCompraDireta != '' ){
    $obDtCompraDireta->setValue ( $stDtCompraDireta );
}else {
    // Se houver um único registro de Entidade, executa comando para preencher Data solicitação
    if ((count($obEntidadeUsuario->obSelect->arOption) == 1) && ($stAcao=="incluir")) {
        $jsOnLoad .= "montaParametrosGET('recuperaUltimaDataContabil', 'inCodEntidade', '');";
    }
}

$obHdnDtCompraDireta = new Hidden();
$obHdnDtCompraDireta->setName( 'HdnDtCompraDireta' );
$obHdnDtCompraDireta->setId  ( 'HdnDtCompraDireta' );
if ($stAcao == "alterar")
    $obHdnDtCompraDireta->setValue( $arCompraDireta['obDtCompraDireta'] );

$obISelectModalidade = new Select();
$obISelectModalidade->setNull    ( false                           );
$obISelectModalidade->setRotulo  ( "Modalidade"                    );
$obISelectModalidade->setTitle   ( "Selecione a modalidade."       );
$obISelectModalidade->setName    ( "inCodModalidade"               );
$obISelectModalidade->setCampoID ( "cod_modalidade"                );
$obISelectModalidade->addOption  ( "","Selecione"                  );
$obISelectModalidade->addOption  ( "8","8 - Dispensa de Licitação" );
$obISelectModalidade->addOption  ( "9","9 - Inexibilidade"         );

$obISelectTipoObjeto = new ISelectTipoObjeto();
$obISelectTipoObjeto->setNull   ( false                              );
$obISelectTipoObjeto->setRotulo ( 'Tipo de Objeto'                   );
$obISelectTipoObjeto->setValue  ( $arCompraDireta['cod_tipo_objeto'] );

$obHdnObjeto = new Hidden();
$obHdnObjeto->setName ( 'hdnObjeto'                   );
$obHdnObjeto->setId   ( 'hdnObjeto'                   );
$obHdnObjeto->setValue( $arCompraDireta['cod_objeto'] );

$obTxtBoxDataEntregaProposta = new Data();
$obTxtBoxDataEntregaProposta->setRotulo( "Data de Entrega da Proposta"                                  );
$obTxtBoxDataEntregaProposta->setTitle ( "Informe a data limite para o fornecedor entregar a proposta." );
$obTxtBoxDataEntregaProposta->setId    ( "stDataEntregaProposta"                                        );
$obTxtBoxDataEntregaProposta->setName  ( "stDataEntregaProposta"                                        );
$obTxtBoxDataEntregaProposta->setNull  ( false                                                          );
$obTxtBoxDataEntregaProposta->setValue ( $arCompraDireta['dt_entrega_proposta']                         );

$obTxtBoxDataValidade = new Data();
$obTxtBoxDataValidade->setRotulo( "Validade da Proposta"                                           );
$obTxtBoxDataValidade->setTitle ( "Informe até que data deve ser válida a proposta do fornecedor." );
$obTxtBoxDataValidade->setId    ( "stDataValidade"                                                 );
$obTxtBoxDataValidade->setName  ( "stDataValidade"                                                 );
$obTxtBoxDataValidade->setNull  ( false                                                            );
$obTxtBoxDataValidade->setValue ( $arCompraDireta['dt_validade_proposta']                          );

$obTxtCondPagamento = new TextBox();
$obTxtCondPagamento->setId        ( "stCondicoesPagamento"                       );
$obTxtCondPagamento->setName      ( "stCondicoesPagamento"                       );
$obTxtCondPagamento->setRotulo    ( "Condições de Pagamento"                     );
$obTxtCondPagamento->setTitle     ( "Informe as Condições de Pagamento."         );
$obTxtCondPagamento->setNull      ( false                                        );
$obTxtCondPagamento->setSize      ( 30                                           );
$obTxtCondPagamento->setMaxLength ( 80                                           );
$obTxtCondPagamento->setValue     ( trim($arCompraDireta['condicoes_pagamento']) );

$obTxtPrazoEntrega = new TextBox;
$obTxtPrazoEntrega->setName      ( "stPrazoEntrega"                                              );
$obTxtPrazoEntrega->setValue     ( ""                                                            );
$obTxtPrazoEntrega->setRotulo    ( "Prazo de Entrega"                                            );
$obTxtPrazoEntrega->setTitle     ( "Informe o prazo para fornecedor entregar o produto/serviço." );
$obTxtPrazoEntrega->setNull      ( false                                                         );
$obTxtPrazoEntrega->setInteiro   ( true                                                          );
$obTxtPrazoEntrega->setMaxLength ( 4                                                             );
$obTxtPrazoEntrega->setSize      ( 20                                                            );
$obTxtPrazoEntrega->setValue     ( $arCompraDireta['prazo_entrega']                              );

$obLblDia = new Label();
$obLblDia->setRotulo( " &nbsp; Dias" );
$obLblDia->setValue ( " &nbsp; Dias" );

$obMapaCompras = new IPopUpMapaCompras( $obForm );
$obMapaCompras->obCampoCod->setId  ( 'stMapaCompras' );
$obMapaCompras->obCampoCod->setName( 'stMapaCompras' );

if ($stAcao == 'incluir') {
    $obMapaCompras->obCampoCod->obEvento->setOnChange( "if (this.value != '') { montaParametrosGET('montaItens','stMapaCompras'); }" );
    $obMapaCompras->obCampoCod->obEvento->setOnBlur  ( "if (this.value != '') { montaParametrosGET('montaItens','stMapaCompras'); }" );
} else {
    $obMapaCompras->obCampoCod->obEvento->setOnChange( "montaParametrosGET('montaItensAlterar','stMapaCompras,hdnMapaCompras');" );
    $obMapaCompras->obCampoCod->obEvento->setOnBlur  ( "montaParametrosGET('montaItensAlterar','stMapaCompras,hdnMapaCompras');" );
}

$stOnBlur  = "if (this.value != '') { montaParametrosGET('validaMapa','stDtCompraDireta, inCodEntidade,  stMapaCompras'); }";
$stOnBlur .= $obMapaCompras->obCampoCod->obEvento->getOnBlur();
$obMapaCompras->obCampoCod->obEvento->setOnBlur( $stOnBlur );
$obMapaCompras->setTipoBusca ( 'verificaMapaComprasDireta' );
$obMapaCompras->setExercicio ( Sessao::getExercicio()      );

if ($arCompraDireta['cod_mapa'] AND $arCompraDireta['exercicio_mapa'])
    $obMapaCompras->obCampoCod->setValue ( $arCompraDireta['cod_mapa']."/".$arCompraDireta['exercicio_mapa'] );

$obMapaCompras->setNull( false );

$obTxtTotalMapa = new Label();
$obTxtTotalMapa->setId     ( "stTotalMapa"   );
$obTxtTotalMapa->setName   ( "stTotalMapa"   );
$obTxtTotalMapa->setRotulo ( "Total do Mapa" );

$obLblDtCompraDireta = new Label();
$obLblDtCompraDireta->setId    ( "obLblDtCompraDireta"               );
$obLblDtCompraDireta->setName  ( "obLblDtCompraDireta"               );
$obLblDtCompraDireta->setRotulo( "Data da Compra Direta"             );
$obLblDtCompraDireta->setValue ( $arCompraDireta['obDtCompraDireta'] );

$obRdoDocumentoFornecedorNao = new Radio();
$obRdoDocumentoFornecedorNao->setName   ( "stDocumentoFornecedor"    );
$obRdoDocumentoFornecedorNao->setId     ( "stDocumentoFornecedorNao" );
$obRdoDocumentoFornecedorNao->setValue  ( "nao"                      );
$obRdoDocumentoFornecedorNao->setRotulo ( "Documento para Cotação"   );
$obRdoDocumentoFornecedorNao->setLabel  ( "Nenhum"                   );
$obRdoDocumentoFornecedorNao->setChecked( true                       );

$obRdoDocumentoFornecedorImprimir = new Radio();
$obRdoDocumentoFornecedorImprimir->setName   ( "stDocumentoFornecedor"         );
$obRdoDocumentoFornecedorImprimir->setId     ( "stDocumentoFornecedorImprimir" );
$obRdoDocumentoFornecedorImprimir->setValue  ( "imprimir"                      );
$obRdoDocumentoFornecedorImprimir->setRotulo ( "Documento para Cotação"        );
$obRdoDocumentoFornecedorImprimir->setLabel  ( "Documento para Imprimir"       );
$obRdoDocumentoFornecedorImprimir->setChecked( false                           );

$obRdoDocumentoFornecedorXML = new Radio();
$obRdoDocumentoFornecedorXML->setName   ( "stDocumentoFornecedor"    );
$obRdoDocumentoFornecedorXML->setId     ( "stDocumentoFornecedorXML" );
$obRdoDocumentoFornecedorXML->setValue  ( "xml"                      );
$obRdoDocumentoFornecedorXML->setRotulo ( "Documento para Cotação"   );
$obRdoDocumentoFornecedorXML->setLabel  ( "Arquivo Para Cotação"     );
$obRdoDocumentoFornecedorXML->setChecked( false                      );

$obSpnItens = new Span;
$obSpnItens->setId( 'spnItens' );

$obTxtMotivoAnulacao = new TextBox();
$obTxtMotivoAnulacao->setId       ( "stMotivoAnulacao"             );
$obTxtMotivoAnulacao->setName     ( "stMotivoAnulacao"             );
$obTxtMotivoAnulacao->setRotulo   ( "Motivo"                       );
$obTxtMotivoAnulacao->setTitle    ( "Informe o Motivo de Anulação" );
$obTxtMotivoAnulacao->setMaxLength( 200                            );
$obTxtMotivoAnulacao->setSize     ( 50                             );
$obTxtMotivoAnulacao->setNull     ( false                          );

$obBtnOk = new Ok();
$obBtnOk->setId( 'Ok' );

$obBtnCancelar = new Cancelar();
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$pgList."?".Sessao::getId()."&stAcao=".substr($request->get('stAcao'),0,strlen($request->get('stAcao'))-1)."','telaPrincipal');" );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Compra Direta" );

if ($boAlteraAnula) {
    $obFormulario->addHidden( $obHdnEntidade );
    $obFormulario->addHidden( $obHdnModalidade );

    $obFormulario->addComponente ( $obLblEntidade );
    $obFormulario->addComponente ( $obLblCompraDireta );
    $obFormulario->addComponente ( $obLblDtCompraDireta );
    $obFormulario->addComponente ( $obLblModalidade );

    if ($stAcao == 'alterar') {
        $obFormulario->addComponente ( $obPopUpProcesso );
        $obFormulario->addComponente ( $obMapaCompras );
        $obFormulario->addComponente ( $obTxtTotalMapa );
    }

    if ($stAcao == "anular") {
        $obFormulario->addComponente ( $obLblProcesso );
    }

    $obFormulario->addHidden( $obHdnCompraDireta );
    $obFormulario->addHidden( $obHdnMapaCompras );
    $obFormulario->addHidden( $obHdnIdMapaCompras );
    $obFormulario->addHidden( $obHdnExercicioMapaCompras );
} else {
    if ($boIdCompraDiretaAutomatica != 't') {
        $obFormulario->addComponente ( $obCodCompraDireta );
    }

    $obFormulario->addComponente ( $obPopUpProcesso );
    $obFormulario->addComponente ( $obMapaCompras );
    $obFormulario->addComponente ( $obTxtTotalMapa );
    $obFormulario->addComponente ( $obEntidadeUsuario );
    $obFormulario->addComponente ( $obDtCompraDireta );
    $obFormulario->addComponente ( $obISelectModalidade );
}

if ($stAcao == "anular") {
    $obFormulario->addComponente ( $obLblTipoObjeto );
    $obFormulario->addComponente ( $obLblObjeto );
    $obFormulario->addComponente ( $obHdnObjeto );
    $obFormulario->addComponente ( $obLblDataEntregaProposta );
    $obFormulario->addComponente ( $obLblDataValidadeProposta );
    $obFormulario->addComponente ( $obLblCondicoesPagamento );
    $obFormulario->addComponente ( $obLblPrazoEntrega );
    $obFormulario->addComponente ( $obLblMapaCompras );
    $obFormulario->addComponente ( $obTxtTotalMapa );
    $obFormulario->addComponente( $obTxtMotivoAnulacao );
} else {
    $obFormulario->addComponente ( $obISelectTipoObjeto );
    $obFormulario->addComponente ( $obLblObjeto );
    $obFormulario->addComponente ( $obHdnObjeto );
    $obFormulario->addComponente ( $obTxtBoxDataEntregaProposta );
    $obFormulario->addComponente ( $obTxtBoxDataValidade );
    $obFormulario->addComponente ( $obTxtCondPagamento );
    $obFormulario->agrupaComponentes( array( $obTxtPrazoEntrega, $obLblDia ) );
    $obFormulario->addComponente ( $obRdoDocumentoFornecedorImprimir );
    $obFormulario->addComponente ( $obRdoDocumentoFornecedorNao );
}

$obFormulario->addHidden ( $obHdnDtCompraDireta );
$obFormulario->addSpan   ( $obSpnItens );

if ($stAcao == 'incluir') {
    $obFormulario->Ok();
} else {
    $obFormulario->defineBarra( array( $obBtnOk, $obBtnCancelar ) );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

if ($boAlteraAnula) {
    echo "<script type='text/javascript'>\n";
    echo "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade=".$arCompraDireta['cod_entidade']."&stMapaCompras=".$arCompraDireta['cod_mapa']."/".$arCompraDireta['exercicio_mapa']."&boAlteraAnula=true','montaItensAlterar');";
    echo "</script>\n";
}

?>
