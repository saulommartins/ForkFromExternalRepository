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
    * Página para exibir sugestao de ata para a funcao Gestão Patrimonial :: Licitação :: Processo Licitatório :: Incluir Ata de Encerramento
    * Data de Criação: 14/01/2009

    * @author Desenvolvedor: Cercato

    * @ignore

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO. 'funcoesLegado.lib.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasMapa.class.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCotacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasMapaItem.class.php';
include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoEdital.class.php';
include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoComissaoLicitacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "BuscaNumeroAta";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

Sessao::write('link', '');

$stCtrl   = $_REQUEST['stCtrl'];
$stAcao   = $_REQUEST['stAcao'];

# Cabeçalho padrão da Ata, sugerido pelo programa.
$stCabecalho  = "[#data_extenso], às [#horario] horas, na sala de licitações do Município de [#municipio], ";
$stCabecalho .= "na [#tipo_logradouro] [#logradouro], n.° [#numero], reuniu-se à Comissão Permanente de Licitações ";
$stCabecalho .= "nomeada pela portaria n.º [#norma], a fim de proceder à abertura";

# Dia por extenso
$stDataExtenso = ucfirst(strtolower(DIAEXTENSO($_REQUEST['dtDataAta'])));
if (!empty($stDataExtenso))
    $stCabecalho = str_replace('[#data_extenso]', $stDataExtenso, $stCabecalho);

# Horário
if (!empty($_REQUEST['stHoraAta']))
    $stCabecalho = str_replace('[#horario]', $_REQUEST['stHoraAta'], $stCabecalho);

# Município
$stFiltro = " WHERE cod_municipio = (
                                        SELECT  valor::integer
                                        FROM  administracao.configuracao
                                        WHERE  parametro = 'cod_municipio'
                                        AND  exercicio = '".Sessao::getExercicio()."'
                                        AND  cod_modulo = 2
                                    )
                AND cod_uf = (
                                SELECT  valor::integer
                                FROM  administracao.configuracao
                                WHERE  parametro = 'cod_uf'
                                AND  exercicio = '".Sessao::getExercicio()."'
                                AND  cod_modulo = 2
                            ) ";

$stNomeMunicipio = SistemaLegado::pegaDado('nom_municipio', 'sw_municipio', $stFiltro);
$stCabecalho = str_replace('[#municipio]', $stNomeMunicipio, $stCabecalho);

# Tipo Logradouro
$stFiltro = "WHERE parametro = 'tipo_logradouro' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$stTipoLogradouro = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
$stCabecalho = str_replace('[#tipo_logradouro]', $stTipoLogradouro, $stCabecalho);

# Logradouro
$stFiltro = "WHERE parametro = 'logradouro' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$stLogradouro = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
$stCabecalho = str_replace('[#logradouro]', $stLogradouro, $stCabecalho);

# Número
$stFiltro = "WHERE parametro = 'numero' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$stNumero = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
$stCabecalho = str_replace('[#numero]', $stNumero, $stCabecalho);

list($inNumEdital, $stExercicioEdital) = explode('/', $_REQUEST['stNumEdital']);

if (!empty($inNumEdital) && !empty($stExercicioEdital)) {
    $obTLicitacaoEdital = new TLicitacaoEdital;
    $obTLicitacaoEdital->setDado('num_edital' , $inNumEdital      );
    $obTLicitacaoEdital->setDado('exercicio'  , $stExercicioEdital);
    $obTLicitacaoEdital->recuperaPorChave($rsLicitacaoEdital);

    # Dados da Licitação.
    $inCodLicitacao       = $rsLicitacaoEdital->getCampo('cod_licitacao');
    $stExercicioLicitacao = $rsLicitacaoEdital->getCampo('exercicio_licitacao');
    $inCodEntidade        = $rsLicitacaoEdital->getCampo('cod_entidade');
    $inCodModalidade      = $rsLicitacaoEdital->getCampo('cod_modalidade');

    # Monta a escrita correta conforme a modalidade.
    switch ($inCodModalidade) {

        case 1:
        case 4:
        case 5:
        case 6:
        case 7:
            $stConjuncao = "do";
        break;

        default:
            $stConjuncao = "da";
        break;
    }

    $stCabecalho .= $stConjuncao." [#modalidade] [#licitacao], destinado ";
    $stCabecalho .= "a aquisição do(s) seguinte(s) item(ns): ";

    # Recupera dados da Licitação.
    $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
    $obTLicitacaoLicitacao->setDado('cod_licitacao'  , $inCodLicitacao      );
    $obTLicitacaoLicitacao->setDado('exercicio'      , $stExercicioLicitacao);
    $obTLicitacaoLicitacao->setDado('cod_entidade'   , $inCodEntidade       );
    $obTLicitacaoLicitacao->setDado('cod_modalidade' , $inCodModalidade     );
    $obTLicitacaoLicitacao->recuperaPorChave($rsLicitacao);

    # Dados do Mapa
    $inCodMapa       = $rsLicitacao->getCampo('cod_mapa');
    $stExercicioMapa = $rsLicitacao->getCampo('exercicio_mapa');

    # Norma
    $obTLicitacaoComissaoLicitacao = new TLicitacaoComissaoLicitacao;
    $obTLicitacaoComissaoLicitacao->setDado('cod_licitacao' , $inCodLicitacao);
    $obTLicitacaoComissaoLicitacao->setDado('exercicio'     , $stExercicioLicitacao);
    $obTLicitacaoComissaoLicitacao->setDado('cod_entidade'  , $inCodEntidade);
    $obTLicitacaoComissaoLicitacao->setDado('cod_modulo'    , $inCodModalidade);
    $obTLicitacaoComissaoLicitacao->recuperaComissaoLicitacao($rsComissaoLicitacao);

    $inCodNorma       = $rsComissaoLicitacao->getCampo('cod_norma');
    $stNorma          = SistemaLegado::pegaDado('num_norma', 'normas.norma', 'WHERE cod_norma = '.$inCodNorma);
    $stExercicioNorma = SistemaLegado::pegaDado('exercicio', 'normas.norma', 'WHERE cod_norma = '.$inCodNorma);
    $stCabecalho      = str_replace('[#norma]', $stNorma.'/'.$stExercicioNorma, $stCabecalho);

    # Modalidade
    $stModalidade = SistemaLegado::pegaDado('descricao', 'compras.modalidade', 'WHERE cod_modalidade = '.$inCodModalidade);
    $stCabecalho  = str_replace('[#modalidade]', $stModalidade, $stCabecalho);

    # Licitação
    $stCabecalho  = str_replace('[#licitacao]', $inCodLicitacao.'/'.$stExercicioLicitacao, $stCabecalho);

}

$obTComprasMapa = new TComprasMapa;
$obTComprasMapa->setDado('cod_mapa'       , $inCodMapa      );
$obTComprasMapa->setDado('exercicio_mapa' , $stExercicioMapa);
$obTComprasMapa->recuperaMapaCotacaoValida($rsCotacaoValida);

# Se tiver uma cotação válida, é porque já "pode" ter sido julgado.
if ($rsCotacaoValida->getNumLinhas() > 0) {
    $obTComprasCotacao = new TComprasCotacao;
    $obTComprasCotacao->setDado('cod_cotacao' , $rsCotacaoValida->getCampo('cod_cotacao')      );
    $obTComprasCotacao->setDado('exercicio'   , $rsCotacaoValida->getCampo('exercicio_cotacao'));
    $obTComprasCotacao->recuperaJulgamentoVencedor($rsItens);

} else {
    # Caso não tenha cotação, busca os ítens do Mapa.
    $obTComprasMapaItem = new TComprasMapaItem;
    $obTComprasMapaItem->setDado('cod_mapa'  , $inCodMapa       );
    $obTComprasMapaItem->setDado('exercicio' , $stExercicioMapa );
    $obTComprasMapaItem->recuperaItensPropostaAgrupado($rsItens);
}

# Inicializa o cabeçalho de itens.
$stCabecalhoItem = '';

while (!$rsItens->eof()) {

    # Detalhes do item.
    if ($rsItens->getCampo('lote') > 0)
        $stCabecalhoItem .= "[Lote] - ".$rsItens->getCampo('lote')." <br />";

    # Sugere a descrição completa.
    if ($rsItens->getCampo('descricao'))
        $stCabecalhoItem .= "[Item] - ".$rsItens->getCampo('descricao')." <br />";

    # Sugere o complemento.
    if ($rsItens->getCampo('complemento'))
        $stCabecalhoItem .= "[Complemento] - ".$rsItens->getCampo('complemento')." <br />";

    # Sugere a marca.
    if ($rsItens->getCampo('marca'))
        $stCabecalhoItem .= "[Marca] - ".$rsItens->getCampo('marca')." <br />";

    # Sugere o valor total.
    if ($rsItens->getCampo('vl_total'))
        $stCabecalhoItem .= "[Valor] - ".$rsItens->getCampo('vl_total')." <br />";

    # Sugere o vencedor do julgamento de propostas.
    if ($rsItens->getCampo('nom_cgm'))
        $stCabecalhoItem .= "[Vencedor] - ".$rsItens->getCampo('nom_cgm')." <br /><br />";

    $rsItens->proximo();
}

# Atribui as substituições na string.
$stTextSugestivo .= $stCabecalho."<br /><br />".$stCabecalhoItem;
$stTextSugestivo = str_replace( "<br />", "\n", $stTextSugestivo );

$obForm = new Form;
$obForm->setAction ( $pgList  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obDescricaoAta = new TextArea;
$obDescricaoAta->setName  ( 'stDescricaoAta' );
$obDescricaoAta->setId    ( 'stDescricaoAta' );
$obDescricaoAta->setRotulo( 'Sugestão' );
$obDescricaoAta->setTitle ( 'Sugestão para a descrição da Ata.' );
$obDescricaoAta->setValue ( $stTextSugestivo );
$obDescricaoAta->setNull  ( false );
$obDescricaoAta->setRows  ( 30 );
$obDescricaoAta->setStyle ( 'width:95%!important;' );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addComponente ($obDescricaoAta);
//$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
