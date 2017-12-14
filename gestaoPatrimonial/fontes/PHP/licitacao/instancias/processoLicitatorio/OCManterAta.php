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
 * Pagina de Oculto para Incluir Edital
 * Data de Criação: 20/01/2009
 *
 *
 * @author Analista:      Gelson Wolowski Gonçalvez <gelson.goncalves@cnm.org.br>
 * @author Desenvolvedor: Diogo Zarpelon            <diogo.zarpelon@cnm.org.br>
 *
 * @ignore

 $Id: OCManterAta.php 65904 2016-06-28 18:33:08Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_LEGADO. 'funcoesLegado.lib.php';

include_once CAM_GP_COM_MAPEAMENTO. 'TComprasMapa.class.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCotacao.class.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasMapaItem.class.php';

include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoLicitacao.class.php';
include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoAta.class.php';
include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoEdital.class.php';
include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoComissaoLicitacao.class.php';
include_once CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoComissaoMembros.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

# Definição do nome dos arquivos em PHP relacionados ao programa.
$stPrograma = "ManterAta";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

//$rq - Utilizado essa nomenclatura de variável porque a variável $request já está em uso
function sugerirDados()
{
    global $request;

    # Cabeçalho padrão da Ata, sugerido pelo programa.
    $stCabecalho  = "[#data_extenso], às [#horario] horas, na sala de licitações do Município de [#municipio], ";
    $stCabecalho .= "na [#tipo_logradouro] [#logradouro], n.° [#numero], reuniu-se à Comissão Permanente de Licitações ";
    $stCabecalho .= "nomeada pela portaria n.º [#norma], a fim de proceder à abertura";

    # Dia por extenso
    $stDataExtenso = ucfirst(strtolower(DIAEXTENSO($request->get('dtDataAta'))));
    if (!empty($stDataExtenso))
        $stCabecalho = str_replace('[#data_extenso]', $stDataExtenso, $stCabecalho);

    # Horário
    if (!is_null($request->get('stHoraAta')))
        $stCabecalho = str_replace('[#horario]', $request->get('stHoraAta'), $stCabecalho);

    # Município
    $stFiltro = " WHERE cod_municipio = (
                                            SELECT  valor
                                              FROM  administracao.configuracao
                                             WHERE  parametro = 'cod_municipio'
                                               AND  exercicio = '".Sessao::getExercicio()."'
                                               AND  cod_modulo = 2
                                        )
                    AND cod_uf = (
                                    SELECT  valor
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

    list($inNumEdital, $stExercicioEdital) = explode('/', $request->get('stNumEdital'));

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
    # Algumas definições para a janela de Alerta.
    $stJs  .= "alertPopUp('Se preferir, copie essa sugestão para usar no cadastro de Ata!', '".$stTextSugestivo."');\n";

    return $stJs;
}

function montaListaVeiculos($arRecordSet , $boExecuta = true)
{
    
    if (is_array($arRecordSet)) {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );
        
        $table = new Table();
        $table->setRecordset   ( $rsRecordSet  );
        $table->setSummary     ( 'Veículos de Publicação'  );
        
        $table->Head->addCabecalho( 'Veículo de Publicação' , 40  );
        $table->Head->addCabecalho( 'Data', 8  );
        $table->Head->addCabecalho( 'Número Publicação', 12  );
        $table->Head->addCabecalho( 'Observação'     , 40  );
        
        $table->Body->addCampo( '[inVeiculo] - [stVeiculo] ' , 'E');
        $table->Body->addCampo( 'dtDataPublicacao', 'C' );
        $table->Body->addCampo( 'inNumPublicacao' );
        $table->Body->addCampo( 'stObservacao'  );
        
        $table->Body->addAcao( 'alterar' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'alterarListaVeiculos', 'id' ) );
        $table->Body->addAcao( 'excluir' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'excluirListaVeiculos', 'id' ) );
        
        $table->montaHTML( true );
        
        if ($boExecuta) {
            return "d.getElementById('spnListaVeiculos').innerHTML = '".$table->getHTML()."';";
        } else {
            return $this->getHTML();
        }
    }
}

switch ($request->get('stCtrl')) {

    case 'validaNumAta':
        $inIdAta        = $request->get('inIdAta');
        $inNumAta       = $request->get('inNumAta');
        $stExercicioAta = Sessao::getExercicio();

        if (!empty($inNumAta) && !empty($stExercicioAta)) {

            $obTLicitacaoAta = new TLicitacaoAta;
            $obTLicitacaoAta->setCampoCod('');
            $obTLicitacaoAta->setComplementoChave('num_ata, exercicio_ata');

            $obTLicitacaoAta->setDado('num_ata'       , $inNumAta             );
            $obTLicitacaoAta->setDado('exercicio_ata' , Sessao::getExercicio());
            $obTLicitacaoAta->recuperaPorChave($rsLicitacaoAta);

            # Caso encontre um número de Ata cadastrado, avisa o usuário e limpa o campo.
            if ($rsLicitacaoAta->getNumLinhas() > 0 && $rsLicitacaoAta->getCampo('id') != $inIdAta) {
                $stJs .= "alertaAviso('Essa Ata já está vinculada ao Edital ".$rsLicitacaoAta->getCampo('num_edital')."/".$rsLicitacaoAta->getCampo('exercicio')."', 'n_incluir', 'erro', '".Sessao::getId()."'); \n";
                $stJs .= "jQuery('#inNumAta').val('').focus(); \n";
                break;
            }
        }
    break;

    # Validação do Edital.
    case "validaEdital" :
        $stNumEdital = $request->get('stNumEdital');
        list($inNumEdital, $stExercicioEdital) = explode('/', $stNumEdital);

        if (empty($stExercicioEdital))
            $stExercicioEdital = Sessao::getExercicio();

        if (is_numeric($inNumEdital) && is_numeric($stExercicioEdital)) {
            $obTLicitacaoEdital = new TLicitacaoEdital;

            $stFiltro = " AND (
                                EXISTS  (
                                          SELECT  1
                                            FROM  compras.julgamento
                                      INNER JOIN  compras.mapa_cotacao
                                              ON  julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                                             AND  julgamento.cod_cotacao = mapa_cotacao.cod_cotacao

                                           WHERE  ll.cod_licitacao = le.cod_licitacao
                                             AND  ll.cod_modalidade = le.cod_modalidade
                                             AND  ll.cod_entidade = le.cod_entidade
                                             AND  ll.exercicio = le.exercicio
                                             AND  ll.exercicio_mapa = mapa_cotacao.exercicio_mapa
                                             AND  ll.cod_mapa = mapa_cotacao.cod_mapa
                                             AND  NOT EXISTS (
                                                               SELECT  1
                                                                 FROM  compras.cotacao_anulada
                                                                WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                                  AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                                                            )
                                        )
                            )";

            $obTLicitacaoEdital->setDado('num_edital'       , $inNumEdital      );
            $obTLicitacaoEdital->setDado('exercicio_edital' , $stExercicioEdital);
            $obTLicitacaoEdital->recuperaListaEdital($rsLicitacaoEdital, $stFiltro);

            # Dados da Licitação.
            $inCodLicitacao       = $rsLicitacaoEdital->getCampo('cod_licitacao');
            $stExercicioLicitacao = $rsLicitacaoEdital->getCampo('exercicio_licitacao');
            $inCodEntidade        = $rsLicitacaoEdital->getCampo('cod_entidade');
            $inCodModalidade      = $rsLicitacaoEdital->getCampo('cod_modalidade');

            # Caso o edital não exista, avisa o usuário e limpa o campo.
            if ($rsLicitacaoEdital->getNumLinhas() < 1) {
                $stJs .= "alertaAviso('Edital inválido ".$stNumEdital."', 'n_incluir', 'erro', '".Sessao::getId()."'); \n";
                $stJs .= "jQuery('#stNumEdital').val('').focus(); \n";
                $stJs .= "jQuery('#btnSugerir').attr('disabled'); \n";
                break;
            }

            # Validação do exercício do Edital que não pode ser maior que o exercício da Ata.
            if ($stExercicioEdital > Sessao::getExercicio()) {
                $stJs .= "alertaAviso('O exercício do edital deve ser anterior ao exercício da Ata', 'n_incluir', 'erro', '".Sessao::getId()."'); \n";
                $stJs .= "jQuery('#stNumEdital').val('').focus(); \n";
                $stJs .= "jQuery('#btnSugerir').attr('disabled'); \n";
                break;
            }

            $obTLicitacaoAta = new TLicitacaoAta;

            $obTLicitacaoAta->setDado('num_edital' , $inNumEdital      );
            $obTLicitacaoAta->setDado('exercicio'  , $stExercicioEdital);
            $obTLicitacaoAta->recuperaAta($rsLicitacaoAtaEdital);

            $inIdAta = $request->get('inIdAta');

            # Caso encontre um número de Ata cadastrado, avisa o usuário e limpa o campo.
            if (($rsLicitacaoAtaEdital->getNumLinhas() > 0  && $rsLicitacaoAtaEdital->getCampo('id') != $inIdAta)
                || ($rsLicitacaoAtaEdital->getNumLinhas() > 0  && $inIdAta == '')){
                $stJs .= "alertaAviso('Esse Edital já está vinculado a Ata ".$rsLicitacaoAtaEdital->getCampo('num_ata')."/".$rsLicitacaoAtaEdital->getCampo('exercicio_ata')."', 'n_incluir', 'erro', '".Sessao::getId()."'); \n";
                $stJs .= "jQuery('#stNumEdital').val('').focus(); \n";
                $stJs .= "jQuery('#btnSugerir').attr('disabled'); \n";
                break;
            }

            $stJs .= "jQuery('#btnSugerir').removeAttr('disabled'); \n";
        } else {
            $stJs .= "alertaAviso('Edital inválido (".$stNumEdital.")', 'n_incluir', 'erro', '".Sessao::getId()."'); \n";
            $stJs .= "jQuery('#stNumEdital').val('').focus(); \n";
            $stJs .= "jQuery('#btnSugerir').attr('disabled'); \n";
        }
    break;

    case 'sugerirDados':
        $stJs = sugerirDados();
    break;

    //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
    case 'carregaListaVeiculos' :
    $arValores = Sessao::read('arValores');
    echo montaListaVeiculos($arValores);
    break;

     //Inclui itens na listagem de veiculos de publicacao utilizados
    case 'incluirListaVeiculos':
    $arValores = Sessao::read('arValores');
    if ($_REQUEST['inVeiculo'] == '') {
        $stMensagem = 'Preencha o campo Veículo de Publicação!';
    }
    
    if ($_REQUEST['dtDataPublicacao'] == '') {
        $stMensagem = 'Preencha o campo Data de Publicação!';
    }
    
    if ($_REQUEST['inNumAta'] == '') {
        $stMensagem = 'Preencha o campo Número da Ata!';
    }

    $boPublicacaoRepetida = false;
    
    if ( is_array( $arValores ) ) {
        foreach ($arValores as $arTEMP) {
        if ($arTEMP['inVeiculo'] == $_REQUEST["inVeiculo"] & $arTEMP['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao']) {
            $boPublicacaoRepetida = true ;
            $stMensagem = "Este veículo de publicação já está na lista.";
        }
        }
    }

    if (!$boPublicacaoRepetida AND !$stMensagem) {
        $inCount = sizeof($arValores);
        
        $arValores[$inCount]['id']               = $inCount + 1;
        $arValores[$inCount]['inVeiculo']        = $_REQUEST[ "inVeiculo"                  ];
        $arValores[$inCount]['stVeiculo']        = $_REQUEST[ "stNomCgmVeiculoPublicacao"  ];
        $arValores[$inCount]['dtDataPublicacao'] = $_REQUEST[ "dtDataPublicacao"           ];
        $arValores[$inCount]['inNumPublicacao']  = $_REQUEST[ "inNumPublicacao"            ];
        $arValores[$inCount]['stObservacao']     = $_REQUEST[ "stObservacao"               ];
        $arValores[$inCount]['ata_id']           = $_REQUEST[ "inNumAta"                   ];
    } else {
        echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    Sessao::write('arValores', $arValores);
    
    echo montaListaVeiculos( $arValores);
    $js.="jq('#HdnCodVeiculo').val('');";
    $js.="jq('#inVeiculo').val('');";
    $js.="jq('#dtDataPublicacao').val('".date('d/m/Y')."');";
    $js.="jq('#inNumPublicacao').val('');";
    $js.="jq('#stObservacao').val('');";
    $js.="jq('#stNomCgmVeiculoPublicacao').html('&nbsp;');";
    $js.="jq('#incluiVeiculo').val('Incluir');";
    $js.="jq('#incluiVeiculo').attr('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao,stNomCgmVeiculoPublicacao, stObservacao, inNumAta\')');";
    echo $js;
    break;

    // limpa os campos da publicacao de veiculos
    case 'limparVeiculo' :
    $js.="jq('#HdnCodVeiculo').val('');";
    $js.="jq('#inVeiculo').val('');";
    $js.="jq('#dtDataPublicacao').val('".date('d/m/Y')."');";
    $js.="jq('#inNumPublicacao').val('');";
    $js.="jq('#stObservacao').val('');";
    $js.="jq('#stNomCgmVeiculoPublicacao').html('&nbsp;');";
    $js.="jq('#incluiVeiculo').val('Incluir');";
    $js.="jq('#incluiVeiculo').attr('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao,stNomCgmVeiculoPublicacao, stObservacao, inNumAta\')');";
    echo $js;
    break;

    //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaVeiculos':
    $boDotacaoRepetida = false;
    $arTEMP            = array();
    $inCount           = 1;
    $arValores = Sessao::read('arValores');
    foreach ($arValores as $value) {
        if ($value['id'] != $_REQUEST['id']) {
            $arTEMP[$inCount]['id'               ] = $inCount;
            $arTEMP[$inCount]['inVeiculo'        ] = $value[ "inVeiculo"        ];
            $arTEMP[$inCount]['stVeiculo'        ] = $value[ "stVeiculo"        ];
            $arTEMP[$inCount]['dtDataPublicacao' ] = $value[ "dtDataPublicacao" ];
            $arTEMP[$inCount]['inNumPublicacao'  ] = $value[ "inNumPublicacao"  ];
            $arTEMP[$inCount]['stObservacao'     ] = $value[ "stObservacao"     ];
            $arTEMP[$inCount]['ata_id'           ] = $value[ "ata_id"           ];
            $inCount++;
       }
    }

    Sessao::write('arValores', $arTEMP);
    echo montaListaVeiculos($arTEMP);
    break;

    //Confirma itens alterados da listagem de veiculos de publicacao utilizados
    case "alteradoListaVeiculos":
    $inCount = 0;
    $boDotacaoRepetida = false;
    $arValores = Sessao::read('arValores');
    
    foreach ($arValores as $key=>$value) {
       if ($value['inVeiculo'] == $_REQUEST["inVeiculo"] && $value['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao'] && ( $value['id'] != $_REQUEST['HdnCodVeiculo'] ) ) {
           $boDotacaoRepetida = true ;
           break;
       }
    }
    
    if (!$boDotacaoRepetida) {
        foreach ($arValores as $key=>$value) {
            if ($value['id'] == $_REQUEST['HdnCodVeiculo']) {
                $arValores[$key]['id'               ] = $_REQUEST[ "HdnCodVeiculo"            ];
                $arValores[$key]['inVeiculo'        ] = $_REQUEST[ "inVeiculo"                ];
                $arValores[$key]['stVeiculo'        ] = $_REQUEST[ "stNomCgmVeiculoPublicacao"];
                $arValores[$key]['dtDataPublicacao' ] = $_REQUEST[ "dtDataPublicacao"         ];
                $arValores[$key]['inNumPublicacao'  ] = $_REQUEST[ "inNumPublicacao"          ];
                $arValores[$key]['stObservacao'     ] = $_REQUEST[ "stObservacao"             ];
            }            
        }
        
        Sessao::write('arValores', $arValores);
        
        $js.=montaListaVeiculos($arValores);
        $js.="jq('#HdnCodVeiculo').val('');";
        $js.="jq('#inVeiculo').val('');";
        $js.="jq('#dtDataPublicacao').val('".date('d/m/Y')."');";
        $js.="jq('#inNumPublicacao').val('');";
        $js.="jq('#stObservacao').val ('');";
        $js.="jq('#stNomCgmVeiculoPublicacao').html('&nbsp;');";
        $js.="jq('#incluiVeiculo').val('Incluir');";
        $js.="jq('#incluiVeiculo').attr('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicacao, stObservacao, inNumAta\')');";
        echo $js;
    } else {
      echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
    }
    break;

    //Carrega itens da listagem de veiculos de publicacao utilizados em seus determinados campos no Form.
    case 'alterarListaVeiculos':
    $arValores = Sessao::read('arValores');
    
    if ( is_array($arValores)) {
        foreach ($arValores as $value) {
            if ($value['id'] == $_REQUEST['id']) {
                $js ="jq('#HdnCodVeiculo').val('".$_REQUEST['id']."');";
                $js.="jq('#inVeiculo').val('".$value['inVeiculo']."');";
                $js.="jq('#dtDataPublicacao').val('".$value['dtDataPublicacao']."');";
                $js.="jq('#inNumPublicacao').val('".$value['inNumPublicacao']."');";
                $js.="jq('#stObservacao').val('".$value['stObservacao']."');";
                $js.="jq('#stNomCgmVeiculoPublicacao').html('".$value['stVeiculo']."');";
                $js.="jq('#incluiVeiculo').val('Alterar');                                                        ";
                $js.="jq('#incluiVeiculo').attr('onclick','montaParametrosGET(\'alteradoListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicacao, stObservacao, HdnCodVeiculo, inNumAta\')');";
                $js.="jq('#incluiVeiculo').focus(); \n";
            }        
        }
    }

    echo $js;
    break;

}

echo $stJs;

?>
