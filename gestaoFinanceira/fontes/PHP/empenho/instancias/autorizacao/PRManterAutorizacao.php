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
    * Página de Processamento de Autorização
    * Data de Criação   : 01/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRManterAutorizacao.php 65418 2016-05-19 13:09:36Z lisiane $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenho.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeEncerramentoMes.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoAutorizacaoEmpenhoAssinatura.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stCaminho = CAM_GF_EMP_INSTANCIAS."autorizacao/OCGeraRelatorioAutorizacao.php";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

//Atributos Dinâmicos
foreach ($arChave as $key=>$value) {
    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
    $inCodAtributo = $arChaves[0];
    if ( is_array($value) )
        $value = implode(",",$value);

    $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
}

$stAcao = $request->get('stAcao');
$obErro = new Erro;

switch ($stAcao) {
    case "material":
    case "incluir":
        //valida a utilização da rotina de encerramento do mês contábil
        $arDtAutorizacao = explode('/', $request->get('stDtAutorizacao'));
        $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
        $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
        $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
        $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
        $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

        if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
            SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
            SistemaLegado::exibeAviso(urlencode("Mês da Autorização encerrado!"),"n_incluir","erro");
            exit;
        }

        if (( $request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3) && ($request->get('inCodDespesa', '') == '') )
            $obErro->setDescricao("Campo Contrapartida inválido!");

        $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );
        if ($request->get('inCodDespesa', '') != '') {
            if ($stFormaExecucao==1 and ($request->get('stCodClassificacao', '') == ''))
                $obErro->setDescricao("Desdobramento não informado!");
            if ( !$obErro->ocorreu() ) {
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
                $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
                $obREmpenhoAutorizacaoEmpenho->setdataEmpenho($request->get('stDtAutorizacao'));
                $obREmpenhoAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
                $obREmpenhoAutorizacaoEmpenho->setTipoEmissao('R');
                $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnteriorDataEmpenho($nuSaldoDotacao);

               if ($request->get('nuVlTotalAutorizacao') > $nuSaldoDotacao)
                   $obErro->setDescricao("O Saldo da Dotação é menor que o Valor Total da Autorização!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 1 );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $request->get('stCodClassificacao') );
            $obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM( $request->get('inCodFornecedor') );
            $obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( $request->get('inCodHistorico') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( $request->get('stDtAutorizacao') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( $request->get('stDtValidadeFinal') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( $request->get('stDtAutorizacao') );
            $obREmpenhoAutorizacaoEmpenho->setDescricao( $request->get('stDescricao') );
            $obREmpenhoAutorizacaoEmpenho->setDtAutorizacao( $request->get('stDtAutorizacao') );
            $nuVlReserva = str_replace('.','',$request->get('hdnVlReserva') );
            $nuVlReserva = str_replace(',','.',$nuVlReserva );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $nuVlReserva );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $request->get('inCodOrgao') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($request->get('inCodUnidadeOrcamento'));
            $obREmpenhoAutorizacaoEmpenho->setCodCategoria($request->get('inCodCategoria'));

            if ($request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3)
                $obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado('conta_contrapartida',$request->get('inCodContrapartida'));

            $arItens = Sessao::read('arItens');                        
            if ( sizeof( $arItens ) ) {
                foreach ($arItens as $arItemPreEmpenho) {
                    $obREmpenhoAutorizacaoEmpenho->addItemPreEmpenho();
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem    ( $arItemPreEmpenho[ 'num_item' ]                                      );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade ( $arItemPreEmpenho[ 'quantidade' ]                                    );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade ( $arItemPreEmpenho[ 'nom_unidade' ]                                   );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal ( $arItemPreEmpenho[ 'vl_total' ]                                      );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem    ( $arItemPreEmpenho[ 'nom_item' ]                                      );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento( $arItemPreEmpenho[ 'complemento' ]                                   );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodMaterial( $arItemPreEmpenho[ 'cod_material' ]                                  );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $arItemPreEmpenho[ 'cod_unidade' ]                  );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $arItemPreEmpenho[ 'cod_grandeza' ]   );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->consultar($rsUnidade);
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsUnidade->getCampo('simbolo')                                     );
                    
                    if($request->get('stTipoItem')=='Catalogo')
                        $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItemPreEmp ( $arItemPreEmpenho[ 'cod_item' ]                               );
                    
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCentroCusto    ( $arItemPreEmpenho[ 'cod_centro' ]                             );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodigoMarca       ( $arItemPreEmpenho[ 'cod_marca' ]                              );
                }
            } else
                $obErro->setDescricao( "É necessário cadastrar pelo menos um Item" );
            
            if ( !$obErro->ocorreu() ){
                $obREmpenhoAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
                $obREmpenhoAutorizacaoEmpenho->setTipoEmissao('R');
                $obErro = $obREmpenhoAutorizacaoEmpenho->incluir($boTransacao);
            }
            
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm.'?&stAcao='.$stAcao, $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()."/".Sessao::getExercicio(), 'incluir', "aviso", Sessao::getId(), "../");
                
                /* Salvar assinaturas configuráveis se houverem */
                $arAssinaturas = Sessao::read('assinaturas');
                if (isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0) {
                    $arAssinatura = $arAssinaturas['selecionadas'];

                    $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'exercicio', $obREmpenhoAutorizacaoEmpenho->stExercicio );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_entidade', $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() );
                    $arPapel = $obTEmpenhoAutorizacaoEmpenhoAssinatura->arrayPapel();

                    foreach ($arAssinatura as $arAssina) {
                        // As assinaturas quando carregam os dados trazem no papel o num_assina, porém quando tu seleciona qualquer um deles
                        // no papel fica a descrição dele, e não o numero, por isso da verificação com o is_string()
                        $inNumAssina = 1;
                        if (isset($arAssina['papel'])) {
                            if (is_string($arAssina['papel']))
                                $inNumAssina = $arPapel[$stPapel];
                            else
                                $inNumAssina = $arAssina['papel'];
                        }
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('num_assinatura', $inNumAssina);
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('numcgm', $arAssina['inCGM']);
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cargo', $arAssina['stCargo']);
                        $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao( $boTransacao );
                    }

                    unset($obTEmpenhoAutorizacaoEmpenhoAssinatura);
                    // Limpa Sessao->assinaturas
                    $arAssinaturas = array( 'disponiveis' => array(), 'papeis' => array(), 'selecionadas' => array() );
                    Sessao::write('assinaturas', $arAssinaturas);
                }

                $stCampos  = $stCaminho."?inCodAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getCodAutorizacao()."&inCodPreEmpenho=".$obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho();
                $stCampos .= "&inCodEntidade=".$request->get('inCodEntidade')."&stExercicio=".Sessao::getExercicio()."&inCodDespesa=".$request->get('inCodDespesa');
                $stCampos .= "&stDtAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getDtAutorizacao()."&stAcao=autorizacao";
                echo "<script>window.location.href='".$stCampos."';</script>";
            } else
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
    case "alterar":
        //valida a utilização da rotina de encerramento do mês contábil
        $arDtAutorizacao = explode('/', $request->get('stDtInclusao'));
        $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
        $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
        $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
        $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
        $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

        if ($request->get('stCodClassificacao') == '' && $request->get('stCodEstrutural') != '')
            $stCodClassificacao = $request->get('stCodEstrutural');
        else
            $stCodClassificacao = $request->get('stCodClassificacao');

        if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
            SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
            SistemaLegado::exibeAviso(urlencode("Mês da Autorização encerrado!"),"n_incluir","erro");
            exit;
        }

        if (( $request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3) && ($request->get('inCodContrapartida', '')=='') )
            $obErro->setDescricao( "Contrapartida não informada!" );

        $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );
        if ($request->get('inCodDespesa', '') != '') {
            if ($stFormaExecucao==1 and (!$stCodClassificacao))
                $obErro->setDescricao("Desdobramento não informado!");

            if ( !$obErro->ocorreu() ) {
                if ($request->get('nuVlTotalAutorizacao') > $request->get('flVlSaldoDotacao'))
                    $obErro->setDescricao("O Saldo da Dotação é menor que o Valor Total da Autorização!");
            }
        }
        
        if (isset($sessao->assinaturas['selecionadas'])) {
            $arSelecionadas = $sessao->assinaturas['selecionadas'];
            foreach ($arSelecionadas as $arAssinaturas) {
                if (!isset($arAssinaturas['papel'])) {
                    $obErro->setDescricao("Selecione o Papel de ".$arAssinaturas['stNomCGM']." na lista de assinaturas.");
                    break;
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stCodClassificacao );
            $obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM( $request->get('inCodFornecedor') );
            $obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setCodHistorico( $request->get('inCodHistorico') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeInicial( $request->get('stDtValidadeInicial') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtValidadeFinal( $request->get('stDtValidadeFinal') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setDtInclusao( $request->get('stDtInclusao') );
            $obREmpenhoAutorizacaoEmpenho->setDescricao( $request->get('stDescricao') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setVlReserva( $request->get('hdnVlReserva') );

            if ($request->get('inCodOrgao') == '' && $request->get('hdnCodOrgao') != '')
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('hdnCodOrgao'));
            else
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('inCodOrgao'));
            
            if ($request->get('inCodUnidadeOrcamento') == '' && $request->get('hdnCodUnidade') != '')
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($request->get('hdnCodUnidade'));
            else
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($request->get('inCodUnidadeOrcamento'));

            $obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
            $obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodReserva( $request->get('inCodReserva') );
            $obREmpenhoAutorizacaoEmpenho->setCodCategoria($request->get('inCodCategoria'));

            if ($request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3)
                $obREmpenhoAutorizacaoEmpenho->obTEmpenhoContrapartidaAutorizacao->setDado('conta_contrapartida',$request->get('inCodContrapartida'));

            $arItens = Sessao::read('arItens');
            if ( sizeof( $arItens ) ) {
                $obTEmpenhoItemPreEmpenho = new TEmpenhoItemPreEmpenho;
                

                foreach ($arItens as $arItemPreEmpenho) {
                    $obREmpenhoAutorizacaoEmpenho->addItemPreEmpenho( $this );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNumItem    ( $arItemPreEmpenho[ 'num_item' ]                                      );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setQuantidade ( $arItemPreEmpenho[ 'quantidade' ]                                    );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomUnidade ( $arItemPreEmpenho[ 'nom_unidade' ]                                   );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setValorTotal ( $arItemPreEmpenho[ 'vl_total' ]                                      );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setNomItem    ( $arItemPreEmpenho[ 'nom_item' ]                                      );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setComplemento( $arItemPreEmpenho[ 'complemento' ]                                   );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodMaterial( $arItemPreEmpenho[ 'cod_material' ]                                  );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $arItemPreEmpenho[ 'cod_unidade' ]                  );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $arItemPreEmpenho[ 'cod_grandeza' ]   );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->consultar($rsUnidade);
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsUnidade->getCampo('simbolo')                                     );
                    if ($request->get('stTipoItem')=='Catalogo')
                        $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodItemPreEmp ( $arItemPreEmpenho[ 'cod_item' ]                               );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCentroCusto    ( $arItemPreEmpenho[ 'cod_centro' ]                             );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodCentroCusto    ( $arItemPreEmpenho[ 'cod_centro' ]                             );
                    $obREmpenhoAutorizacaoEmpenho->roUltimoItemPreEmpenho->setCodigoMarca       ( $arItemPreEmpenho[ 'cod_marca' ]                              );
                    
                    $obTEmpenhoItemPreEmpenho->setDado( "exercicio"       , Sessao::getExercicio()          );
                    $obTEmpenhoItemPreEmpenho->setDado( "cod_pre_empenho" , $request->get('inCodPreEmpenho'));
                    $obTEmpenhoItemPreEmpenho->setDado( "num_item"        , $arItemPreEmpenho[ 'num_item' ] );
                    $obTEmpenhoItemPreEmpenho->recuperaPorChave($rsRecordSet, $boTransacao);
                    
                    //Altera a marca do Item ticket #23755
                    if ($rsRecordSet->getNumLinhas()>0) {
                        $obTEmpenhoItemPreEmpenho->setDado( "cod_marca"    , $arItemPreEmpenho[ 'cod_marca' ]        );
                        $obTEmpenhoItemPreEmpenho->setDado( "cod_unidade"  , $rsRecordSet->getCampo( 'cod_unidade'  ));
                        $obTEmpenhoItemPreEmpenho->setDado( "cod_grandeza" , $rsRecordSet->getCampo( 'cod_grandeza' ));
                        $obTEmpenhoItemPreEmpenho->setDado( "quantidade"   , $rsRecordSet->getCampo( 'quantidade'   ));
                        $obTEmpenhoItemPreEmpenho->setDado( "nom_unidade"  , $rsRecordSet->getCampo( 'nom_unidade'  ));
                        $obTEmpenhoItemPreEmpenho->setDado( "sigla_unidade", $rsRecordSet->getCampo( 'sigla_unidade'));
                        $obTEmpenhoItemPreEmpenho->setDado( "vl_total"     , $rsRecordSet->getCampo( 'vl_total'     ));
                        $obTEmpenhoItemPreEmpenho->setDado( "nom_item"     , $rsRecordSet->getCampo( 'nom_item'     ));
                        $obTEmpenhoItemPreEmpenho->setDado( "complemento"  , $rsRecordSet->getCampo( 'complemento'  ));
                        $obTEmpenhoItemPreEmpenho->alteracao($boTransacao);
                    }
                }
            } else
                $obErro->setDescricao( "É necessário cadastrar pelo menos um Item" );
            
            if ( !$obErro->ocorreu() )
                $obREmpenhoAutorizacaoEmpenho->setCodEntidade($request->get('inCodEntidade'));
                $obREmpenhoAutorizacaoEmpenho->setTipoEmissao('R');
                $obErro = $obREmpenhoAutorizacaoEmpenho->alterar(); 

            /* Excluir Assinaturas vinculadas ao documento */
            if ( !$obErro->ocorreu() ) {
                /* Montar um RecordSet com todas as assinaturas vinculadas ao documento na tabela correspondente */
                $obTAutorizacaoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                $obTAutorizacaoAssinatura->setDado( 'exercicio', $obREmpenhoAutorizacaoEmpenho->stExercicio );
                $obTAutorizacaoAssinatura->setDado( 'cod_entidade', $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                $obTAutorizacaoAssinatura->setDado( 'cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() );
                $rsRecordSet = new RecordSet;
                $obTAutorizacaoAssinatura->recuperaAssinaturasAutorizacao( $rsRecordSet, '', '', '' );
                /* Excluir um por um os Itens das Assinaturas */
                $obTEAutorizacaoEmpAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                $obTEAutorizacaoEmpAssinatura->setDado( 'exercicio', $obTAutorizacaoAssinatura->getDado('exercicio') );
                $obTEAutorizacaoEmpAssinatura->setDado( 'cod_entidade', $obTAutorizacaoAssinatura->getDado('cod_entidade') );
                $obTEAutorizacaoEmpAssinatura->setDado( 'cod_autorizacao', $obTAutorizacaoAssinatura->getDado('cod_autorizacao') );
                
                while ($rsRecordSet->each() ) {
                    $arAssinaturaBanco = $rsRecordSet->getObjeto();
                    $obTEAutorizacaoEmpAssinatura->setDado( 'num_assinatura', $arAssinaturaBanco['num_assinatura'] );
                    $obErro = $obTEAutorizacaoEmpAssinatura->exclusao();
                }
            }

            /* Salvar assinaturas configuráveis se houverem */
            if ( !$obErro->ocorreu() ) {
                $arAssinaturas = Sessao::read('assinaturas');
                if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {
                    $arAssinatura = $arAssinaturas['selecionadas'];
                    // Array configurado de acordo com o lay-out do documento que será emitido (ver documento impresso)
                    $arPapel = array( 'autorizo'=>1, 'autorizoempenho'=>2 );

                    $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'exercicio', $obREmpenhoAutorizacaoEmpenho->stExercicio );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_entidade', $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cod_autorizacao', $obREmpenhoAutorizacaoEmpenho->getCodAutorizacao() );

                    foreach ($arAssinatura as $arAssina) {
                        $stPapel = (isset($arAssina['papel'])) ? $arAssina['papel'] : '';
                        $inNumAssina = (isset($arPapel[$stPapel])) ? $arPapel[$stPapel] : 1;
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'num_assinatura', $inNumAssina );
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                        $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                        $obErro = $obTEmpenhoAutorizacaoEmpenhoAssinatura->inclusao( $boTransacao );
                    }
                }
                // Limpa Sessao->assinaturas
                $arAssinaturas = array( 'disponiveis'=>array(), 'papeis'=>array(), 'selecionadas'=>array() );
                Sessao::write('assinaturas', $arAssinaturas);
            }

            if ( !$obErro->ocorreu() ) {
                $stFiltro = "";
                $arFiltro = Sessao::read('filtro');
                foreach ($arFiltro as $stCampo => $stValor) {
                    $stFiltro .= $stCampo."=".@urlencode( $stValor )."&";
                }

                $stFiltro .= "pg=".Sessao::read('pg')."&";
                $stFiltro .= "pos=".Sessao::read('pos')."&";
                $stFiltro .= "stAcao=".$request->get('stAcao');

                if ( !$obErro->ocorreu() ) {
                    SistemaLegado::alertaAviso($pgList."?".$stFiltro, $request->get('inCodAutorizacao')."/".Sessao::getExercicio(), "alterar", "aviso", Sessao::getId(), "../");

                    $stCampos  = $stCaminho."?inCodAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getCodAutorizacao();
                    $stCampos .= "&inCodPreEmpenho=".$obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho()."&inCodEntidade=".$request->get('inCodEntidade');
                    $stCampos .= "&inCodDespesa=".$request->get('inCodDespesa')."&stAcao=autorizacao&stExercicio=".Sessao::getExercicio();

                    echo "<script>window.location.href='".$stCampos."';</script>";
                } else
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            } else
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
    case "anular":
        //valida a utilização da rotina de encerramento do mês contábil
        $arDtAutorizacao = explode('/', date('d/m/').Sessao::getExercicio());
        $boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
        $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
        $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
        $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
        $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

        if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
            SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
            SistemaLegado::exibeAviso(urlencode("Mês da Autorização encerrado!"),"n_incluir","erro");
            exit;
        }

        $obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
        $obREmpenhoAutorizacaoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodReserva( $request->get('inCodReserva') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoReserva->setCodDespesa( $request->get('inCodDespesa') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
        $obREmpenhoAutorizacaoEmpenho->setExercicio( $request->get('stExercicio') );
        $obREmpenhoAutorizacaoEmpenho->setMotivoAnulacao( $request->get('stMotivo')." - Autorização de Empenho: ".$request->get('inCodAutorizacao')."/".Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->setDtAnulacao( date('d/m/').Sessao::getExercicio() );
        $obErro = $obREmpenhoAutorizacaoEmpenho->anular();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".@urlencode( $stValor )."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$request->get('stAcao');

        if ( !$obErro->ocorreu() ) {
           SistemaLegado::alertaAviso( $pgList."?stAcao=anular&".$stFiltro, $request->get('inCodAutorizacao').'/'.$request->get('stExercicio'),"excluir","aviso",Sessao::getId(),"../");

           $stCampos  = $stCaminho."?inCodAutorizacao=".$obREmpenhoAutorizacaoEmpenho->getCodAutorizacao();
           $stCampos .= "&inCodPreEmpenho=".$obREmpenhoAutorizacaoEmpenho->getCodPreEmpenho()."&inCodEntidade=".$request->get('inCodEntidade');
           $stCampos .= "&inCodDespesa=".$request->get('inCodDespesa')."&stExercicio=".$request->get('stExercicio')."&stAcao=anulacao";

           echo "<script>window.location.href='".$stCampos."';</script>";
        } else
            SistemaLegado::alertaAviso( $pgList."?stAcao=anular&".$stFiltro, urlencode($obErro->getDescricao()), "n_excluir","erro",Sessao::getId(),"../" );
    break;
}

if ($obErro->ocorreu())
    SistemaLegado::executaFrameOculto(" window.parent.frames['telaPrincipal'].document.getElementById('Ok').disabled = false; ");
?>
