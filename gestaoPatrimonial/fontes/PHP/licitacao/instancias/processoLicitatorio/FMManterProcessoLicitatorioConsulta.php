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

    * Página de Formulário para incluir processo licitatório
    * Data de Criação   : 04/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    $Id: FMManterProcessoLicitatorio.php 33137 2008-09-06 18:15:59Z luiz $

    * Casos de uso : uc-03.04.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"      );
include_once(CAM_GP_COM_COMPONENTES."IPopUpObjeto.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectTipoLicitacao.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectCriterioJulgamento.class.php");
include_once(CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php");
include_once(CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectComissao.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectComissaoEquipeApoio.class.php");
include_once(CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php" );
include_once(CAM_GP_LIC_COMPONENTES."ISelectDocumento.class.php" );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");

//Definições padrões do framework
$stPrograma = "ManterProcessoLicitatorio";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgCons     = "FM".$stPrograma."Consulta.php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once($pgOcul);
include_once($pgJs);

Sessao::write('arMembros', array());
Sessao::write('arMembro', array());
Sessao::write('arDocumentos', array());
Sessao::write('arDocumentosExcluidos', array());

$stFiltroPg = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltroPg = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltroPg .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltroPg .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

$stAcao = $request->get('stAcao');

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

$obForm = new Form;
$obForm->setAction ( $pgList );

$inCodEntidade        = $_REQUEST['inCodEntidade'];
$inCodModalidade      = $_REQUEST['inCodModalidade'];
$inCodLicitacao       = $_REQUEST['inCodLicitacao'];
$stExercicioLicitacao = $_REQUEST['stExercicioLicitacao'];

$obTLicitacaoLicitacaoAutorizacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacaoAutorizacao->setDado( 'inCodEntidade'      , $inCodEntidade);
$obTLicitacaoLicitacaoAutorizacao->setDado( 'inCodLicitacao'      , $inCodLicitacao);
$obTLicitacaoLicitacaoAutorizacao->setDado( 'stExercicioLicitacao', $stExercicioLicitacao);
$obTLicitacaoLicitacaoAutorizacao->setDado( 'inCodModalidade' , $inCodModalidade);
$obTLicitacaoLicitacaoAutorizacao->recuperaAutorizacaoLicitacao($rsAutorizacaoLicitacao);
# Buscando a descrição
$arMapa = explode ( '/', $_REQUEST['stMapaCompra'] );


$obTLicitacaoLicitacao =  new TLicitacaoLicitacao;
$obTLicitacaoLicitacao->setDado('cod_objeto'      , $_REQUEST['stCodObjeto']);
$obTLicitacaoLicitacao->setDado('cod_mapa'        , $arMapa[0]  );
$obTLicitacaoLicitacao->setDado('exercicio'       , $arMapa[1]);
$obTLicitacaoLicitacao->setDado('cod_tipo_objeto' , $_REQUEST['inCodTipoObjeto']);
$obTLicitacaoLicitacao->setDado('cod_criterio'    , $_REQUEST['inCodCriterio']);
$obTLicitacaoLicitacao->recuperaDescricaoJulgamentoObjeto($rsLicitacaoLicitacao);

//Recupera Todos
$stFiltro  = " where licitacao.cod_licitacao  =" . $inCodLicitacao  ;
$stFiltro .= "   and licitacao.cod_modalidade =" . $inCodModalidade  ;
$stFiltro .= "   and licitacao.cod_entidade   =" . $inCodEntidade  ;
$stFiltro .= "   and licitacao.exercicio      ='" . $stExercicioLicitacao . "'";

$obTLicitacaoLicitacao = new TLicitacaoLicitacao();
$obTLicitacaoLicitacao->recuperaTodos ( $rsLicitacao , $stFiltro);

list($ano, $mes, $dia) = explode("-", substr($rsLicitacao->getCampo('timestamp'), 0, 10));
$stDtLicitacao = $dia."/".$mes."/".$ano;

# Recupera a comissão Permanente e a comissão de Apoio vinculadas a licitação.
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoComissaoLicitacao.class.php");
$obTLicitacaoComissao = new TLicitacaoComissaoLicitacao;

$obTLicitacaoComissao->setDado('cod_licitacao'  , $inCodLicitacao       );
$obTLicitacaoComissao->setDado('cod_modalidade' , $inCodModalidade      );
$obTLicitacaoComissao->setDado('cod_entidade'   , $inCodEntidade        );
$obTLicitacaoComissao->setDado('exercicio'      , $stExercicioLicitacao );

$obTLicitacaoComissao->recuperaComissaoLicitacao($rsLicitacaoComissao);

while (!$rsLicitacaoComissao->eof()) {
    # Preenche a variável com a descrição da Comissão Permanente.
    if ($rsLicitacaoComissao->getCampo('cod_tipo_comissao') <> 4) {
        $stComissaoLicitacao = $rsLicitacaoComissao->getCampo('finalidade')." (Vigência: " .$rsLicitacaoComissao->getCampo('dt_publicacao')." ".$rsLicitacaoComissao->getCampo('dt_termino').")";
    } elseif ($rsLicitacaoComissao->getCampo('cod_tipo_comissao') == 4) {
        $stComissaoLicitacaoApoio = $rsLicitacaoComissao->getCampo('finalidade')." (Vigência: " .$rsLicitacaoComissao->getCampo('dt_publicacao')." ".$rsLicitacaoComissao->getCampo('dt_termino').")";
    }

    $rsLicitacaoComissao->proximo();
}

# Recupera os Membros Adicionais para exibir na consulta.
include_once(TLIC."TLicitacaoMembroAdicional.class.php");
$obTLicitacaoMembroAdicional = new TLicitacaoMembroAdicional;
$obTLicitacaoMembroAdicional->setDado('cod_licitacao',$inCodLicitacao);
$obTLicitacaoMembroAdicional->setDado('cod_entidade',trim($entidade[0]));
$obTLicitacaoMembroAdicional->setDado('exercicio',Sessao::getExercicio());
$obTLicitacaoMembroAdicional->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
$obTLicitacaoMembroAdicional->recuperaMembroAdicional($rsMembroAdicional);

$inCount = 0;
$arMembro = array();

while (!$rsMembroAdicional->eof()) {
    $arMembro[$inCount]['nom_cgm'] = $rsMembroAdicional->getCampo('nom_cgm');
    $arMembro[$inCount]['num_cgm'] = $rsMembroAdicional->getCampo('numcgm');
    $arMembro[$inCount]['adicional'] = 'Sim';
    $inCount++;
    $rsMembroAdicional->proximo();
}

$jsOnLoad .= montaListaMembroAdicionalConsulta($arMembro);

# Busca os documentos vinculados a Licitação.
include_once(TLIC."TLicitacaoLicitacaoDocumentos.class.php");
$obTLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos();
$obTLicitacaoDocumentos->setDado('cod_licitacao',$inCodLicitacao);
$obTLicitacaoDocumentos->setDado('cod_entidade',trim($entidade[0]));
$obTLicitacaoDocumentos->setDado('exercicio',Sessao::getExercicio());
$obTLicitacaoDocumentos->setDado('cod_modalidade',$inCodModalidade);
$obTLicitacaoDocumentos->recuperaDocumentosLicitacao($rsDocumentosLicitacao);

$inCount = 0;
while (!$rsDocumentosLicitacao->eof()) {
    $arDocumentos[$inCount]['nom_documento'] = $rsDocumentosLicitacao->getCampo('nom_documento');
    $arDocumentos[$inCount]['cod_documento'] = $rsDocumentosLicitacao->getCampo('cod_documento');
    $arDocumentos[$inCount]['modalidade'] = 'true';
    $inCount++;
    $rsDocumentosLicitacao->proximo();
}

$jsOnLoad .= montaListaDocumentoConsulta($arDocumentos);

# Define o Label da Data da Licitação
$obLblDtLicitacao = new Label;
$obLblDtLicitacao->setRotulo('Data da Licitação');
$obLblDtLicitacao->setValue($stDtLicitacao);

# Define o Label de valor total da referência
$obLblValorReferencia = new Label;
$obLblValorReferencia->setName('flValorReferencia');
$obLblValorReferencia->setId( 'stValorReferencia' );
$obLblValorReferencia->setRotulo('Valor Total de Referência');
$obLblValorReferencia->setValue( number_format( $_REQUEST['vlCotado'] , 2, ',', '.') );

# Define o Label de Modalidade
$obLblModalidade = new Label;
$obLblModalidade->setRotulo('Modalidade');
$obLblModalidade->setValue($_REQUEST['stModalidade']);

include_once(TLIC."TLicitacaoTipoChamadaPublica.class.php");
$obTLicitacaoTipoChamadaPublica = new TLicitacaoTipoChamadaPublica;
$obTLicitacaoTipoChamadaPublica->setDado('cod_tipo',$rsLicitacao->getCampo("tipo_chamada_publica"));
$obTLicitacaoTipoChamadaPublica->recuperaPorChave($rsTipoChamadaPublica);

# Define o Label de Tipo de Chamada Pública
$obLblChamadaPublica = new Label;

if ($rsLicitacao->getCampo('cod_modalidade') == 8 || $rsLicitacao->getCampo('cod_modalidade') == 9) {
    $obLblChamadaPublica->setRotulo('Chamada Pública');
    if ($rsTipoChamadaPublica->getCampo("cod_tipo") != 0) {
        $obLblChamadaPublica->setValue("Sim");
    } else {
        $obLblChamadaPublica->setValue("Não");
    }
} else {
    $obLblChamadaPublica->setRotulo('Tipo de Chamada Pública');
    $obLblChamadaPublica->setValue($rsTipoChamadaPublica->getCampo("cod_tipo")." - ".$rsTipoChamadaPublica->getCampo("descricao"));
}

if ($rsLicitacao->getCampo('cod_modalidade') == 3 ||
    $rsLicitacao->getCampo('cod_modalidade') == 6 ||
    $rsLicitacao->getCampo('cod_modalidade') == 7 ) {

    # Define o Label de Registro de Preços
    $obLblRegistroPreco = new Label;
    $obLblRegistroPreco->setRotulo('Registro de Preços');
    $obLblRegistroPreco->setValue($rsLicitacao->getCampo("registro_precos") == 't' ? 'Sim' : 'Não');
}

# Define o Label para tipo de Cotação
$obLblTipoCotacao = new Label;
$obLblTipoCotacao->setName ( 'txtTipoCotacao' );
$obLblTipoCotacao->setId   ( 'stTipoCotacao' );
$obLblTipoCotacao->setRotulo ( 'Tipo Cotação' );
$obLblTipoCotacao->setValue ($rsLicitacaoLicitacao->getCampo('mapa_cod_tipo_licitacao')." - ".$rsLicitacaoLicitacao->getCampo('tipo_licitacao'));

# Define objeto de select critério julgamento
$obILblCriterioJulgamento = new Label;
$obILblCriterioJulgamento->setRotulo('Critério do Julgamento');
$obILblCriterioJulgamento->setValue($_REQUEST['inCodCriterio']." - ".$rsLicitacaoLicitacao->getCampo('descricao_criterio_julgamento'));

# Define objeto de popup objeto
$obLblObjeto = new Label;
$obLblObjeto->setRotulo('Objeto');
$obLblObjeto->setValue($_REQUEST['stCodObjeto']." - ".$rsLicitacaoLicitacao->getCampo('descricao_objeto'));

# Define objeto de select tipo Objeto
$obILblTipoObjeto = new Label;
$obILblTipoObjeto->setRotulo('Tipo de Objeto');
$obILblTipoObjeto->setValue($rsLicitacaoLicitacao->getCampo('descricao_tipo_objeto'));

# Define objeto span para objeto valor maximo/minimo
$obLblMaxMin = new Label;
$obLblMaxMin->setName  ('stValor');
$obLblMaxMin->setValue (number_format($_REQUEST['vlCotado'], 2 , ',' , '.'));
if (trim($codModalidade[0])  == 4) {
    $obLblMaxMin->setRotulo('Valor Mínimo');
} else {
    $obLblMaxMin->setRotulo('Valor Máximo');
}

$obLblDataHomogacao = new Label;
$obLblDataHomogacao->setName  ('stDataHomologacao');
$obLblDataHomogacao->setRotulo('Data da Homologação');
$obLblDataHomogacao->setValue ($_REQUEST["dtHomologacao"]);

Sessao::write("stEntidade", $_REQUEST["stEntidade"] );
Sessao::write("stProcesso", $_REQUEST["stProcesso"] );
Sessao::write("inCodLicitacao", $_REQUEST["inCodLicitacao"] );
Sessao::write("stModalidade", $_REQUEST["stModalidade"] );
Sessao::write("stCodObjeto", $_REQUEST["stCodObjeto"] );
Sessao::write("inCodTipoObjeto", $_REQUEST["inCodTipoObjeto"] );
Sessao::write("inCodRegime", $_REQUEST["inCodRegime"] );
Sessao::write("stUnidadeOrcamentaria", $_REQUEST["stUnidadeOrcamentaria"] );
Sessao::write("inCodComissao", $_REQUEST["inCodComissao"] );
Sessao::write("inCodTipoLicitacao", $_REQUEST["inCodTipoLicitacao"] );
Sessao::write("inCodCriterio", $_REQUEST["inCodCriterio"] );
Sessao::write("stExercicioLicitacao", $_REQUEST["stExercicioLicitacao"] );
Sessao::write("inCodEntidade", $_REQUEST["inCodEntidade"] );
Sessao::write("inCodModalidade", $_REQUEST["inCodModalidade"] );
Sessao::write("dt_homologacao", $_REQUEST["dtHomologacao"] );

$obLnknAutorizacao = new Link;
$obLnknAutorizacao->setRotulo ("Dados da Autorização" );
$obLnknAutorizacao->setValue  ("Relatório"       );
$obLnknAutorizacao->setTarget ("oculto"           );
$obLnknAutorizacao->setHref   (  CAM_GP_LIC_PROCESSOLICITATORIO."OCDadosLicitacao.php");

$stProcesso = explode ("/",$_REQUEST['stProcesso']);

# Define label Processo Administrativo.
$obLblProcesso = new Label;
$obLblProcesso->setRotulo('Processo Administrativo');
$obLblProcesso->setValue($stProcesso[0]."/".$stProcesso[1]);

# Define label de Comissão de Licitação.
$obLblComissao = new Label;
$obLblComissao->setRotulo ('Comissão de Licitação');
$obLblComissao->setValue  ($stComissaoLicitacao);

# Define label de Comissão de Apoio.
$obLblComissaoEquipeApoio = new Label;
$obLblComissaoEquipeApoio->setRotulo ('Equipe de Apoio');
$obLblComissaoEquipeApoio->setValue  ($stComissaoLicitacaoApoio);

$obSpnItens = new Span;
$obSpnItens->setId( 'spnItens' );

$obSpnMembros = new Span;
$obSpnMembros->setId ( 'spnMembros' );

$obSpnMembroAdicional = new Span;
$obSpnMembroAdicional->setId( "spnMembroAdicional" );

$obSpnDocumento = new Span;
$obSpnDocumento->setId( "spnDocumento" );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($_REQUEST['stEntidade']);

$obLblLicitacao = new Label;
$obLblLicitacao->setRotulo('Número da Licitação');
$obLblLicitacao->setValue($inCodLicitacao);

$obHdnstAcao = new Hidden;
$obHdnstAcao->setName('stAcao');
$obHdnstAcao->setValue($stAcao);

$obLblMapaCompra = new Label;
$obLblMapaCompra->setRotulo('Mapa de Compras');
$obLblMapaCompra->setValue($_REQUEST['stMapaCompra']);

$obTLicitacaoLicitacaoStatus = new TLicitacaoLicitacao();
$obTLicitacaoLicitacaoStatus->setDado( 'inCodEntidade'      , $inCodEntidade);
$obTLicitacaoLicitacaoStatus->setDado( 'inCodLicitacao'      , $inCodLicitacao);
$obTLicitacaoLicitacaoStatus->setDado( 'stExercicioLicitacao', $stExercicioLicitacao);
$obTLicitacaoLicitacaoStatus->setDado( 'inCodModalidade' , $inCodModalidade);
$obTLicitacaoLicitacaoStatus->recuperaStatusLicitacao($rsStatusLicitacao);

$obLblSituacao = new Label;
$obLblSituacao->setName  ('stSituacao');
$obLblSituacao->setRotulo('Situação');
$obLblSituacao->setValue ($rsStatusLicitacao->getCampo('status'));

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltroPg;
$obBtnVoltar = new Button;
$obBtnVoltar->setName  ( "Voltar" );
$obBtnVoltar->setValue ( "Voltar" );
$obBtnVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

# Define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                           );

# Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->setAjuda         ( "UC-03.05.15"                     );
$obFormulario->addTitulo        ( "Dados da Licitação"              );
$obFormulario->addComponente    ( $obLblEntidade                    );
$obFormulario->addComponente    ( $obLblLicitacao                   );
$obFormulario->addComponente    ( $obLblProcesso                    );
$obFormulario->addComponente    ( $obLblMapaCompra  );
$obFormulario->addComponente    ( $obLblDtLicitacao  );
$obFormulario->addComponente    ( $obLblValorReferencia);
$obFormulario->addComponente    ( $obLblTipoCotacao  );
$obFormulario->addComponente    ( $obLblModalidade	);

if ($rsLicitacao->getCampo('cod_modalidade') == 8 ||
    $rsLicitacao->getCampo('cod_modalidade') == 9 ||
    $rsLicitacao->getCampo('cod_modalidade') == 10 ) {
    $obFormulario->addComponente ( $obLblChamadaPublica );
}

if ($rsLicitacao->getCampo('cod_modalidade') == 3 ||
    $rsLicitacao->getCampo('cod_modalidade') == 6 ||
    $rsLicitacao->getCampo('cod_modalidade') == 7 ) {
    $obFormulario->addComponente ( $obLblRegistroPreco );
}

$obFormulario->addComponente    ( $obILblCriterioJulgamento);
$obFormulario->addComponente    ( $obILblTipoObjeto);
$obFormulario->addComponente    ( $obLblObjeto );
$obFormulario->addComponente    ( $obLblMaxMin );
if($_REQUEST["dtHomologacao"] != ''){
    $obFormulario->addComponente    ($obLblDataHomogacao);
    if($rsAutorizacaoLicitacao->getCampo('autorizacao') != '') {
         $obFormulario->addComponente    ($obLnknAutorizacao ); 
    }
}
$obFormulario->addComponente    ($obLblSituacao);
$obFormulario->addSpan   ( $obSpnItens );
$obFormulario->addTitulo        ( "Dados da Comissão de Licitação"  );
$obFormulario->addcomponente    ( $obLblComissao                    );
$obFormulario->addcomponente    ( $obLblComissaoEquipeApoio         );
$obFormulario->addSpan          ( $obSpnMembros                     );
$obFormulario->addSpan          ( $obSpnMembroAdicional             );
$obFormulario->addSpan          ( $obSpnDocumento                   );
$obFormulario->addHidden        ( $obHdnstAcao                       );

$obFormulario->defineBarra( array( $obBtnVoltar ) );
$obFormulario->show();

# Monta o Span da Comissão dos participantes.
$jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stAcao=".$stAcao."&stExercicioLicitacao=".$stExercicioLicitacao."&inCodEntidade=".$inCodEntidade."&inCodLicitacao=".$inCodLicitacao."&inCodModalidade=".$inCodModalidade."','consultaComissaoMembros');";

$jsOnLoad .="ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao=".$stExercicioLicitacao."&inCodEntidade=".$inCodEntidade."&inCodLicitacao=".$inCodLicitacao."&stMapaCompras=".$arMapa[0]."/".$arMapa[1]."&boAlteraAnula=true','montaItensAlterar');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
