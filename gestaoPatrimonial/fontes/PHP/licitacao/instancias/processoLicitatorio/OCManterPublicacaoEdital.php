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
    * Pagina Oculta para Formulário de Veículos de Publicaçao do Edital
    * Data de Criação   : 09/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fabio Moreira da Silva

    * @ignore

    * Casos de uso: uc-03.05.17

    $Id: OCManterPublicacaoEdital.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//Componentes utilizados
include_once(CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php");
//classes de mapeamento
include_once(TLIC."TLicitacaoEdital.class.php");
include_once(TLIC."TLicitacaoPublicacaoEdital.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterPublicacaoEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];


/*
* Ajusta o formulario para alteracao de veículo
*/
function montaAlteracao($id)
{
    $arVeiculos = Sessao::read('arVeiculos');

    $stJs.="document.getElementById('hdnId').value = '".$id."';";
    $stJs.="document.getElementById('nomeVeiculoPublicacao').value = '';";

    $stJs.="el = document.getElementById('nomeVeiculoPublicacao'); ";
    $nome = explode("-",$arVeiculos[$id]['nomeVeiculoPublicacao']);
    $stJs.="el.innerHTML = '".trim($nome[1])."'; ";

    $stJs.="document.getElementById('hdnNomeVeiculo').value = '".trim($nome[1])."'; ";

    $stJs.="el = document.getElementById('veiculoPublicacao'); ";
    $stJs.="el.value = '".$arVeiculos[$id]['veiculoPublicacao']."'; ";
    $stJs.="el.focus();";

    $stJs.="el = document.getElementById('dataPublicacao'); ";
    $stJs.="el.value = '".$arVeiculos[$id]['dataPublicacao']."'; ";
    
    $stJs.="el = document.getElementById('inNumPublicacao'); ";
    $stJs.="el.value = '".$arVeiculos[$id]['inNumPublicacao']."'; ";

    $stJs.="elObs = document.getElementById('observacao'); ";
    $stJs.="elObs.value = '".str_replace('&#039;',"'",html_entity_decode($arVeiculos[$id]['observacao']))."'; ";

    $stJs.= "document.frm.btLimparVeiculoPublicacao.disabled = false; ";
    //$stJs.= "document.frm.btIncluirVeiculoPublicacao.setAttribute('onclick','montaParametrosGET( \'AlterarBotao\', \'hdnDataEdital,hdnId, veiculoPublicacao, hdnNomeVeiculo, dataPublicacao, observacao\')');";
    $stJs.= "document.frm.btIncluirVeiculoPublicacao.value='Alterar'; ";
    $stJs.= "document.frm.btIncluirVeiculoPublicacao.setAttribute('onclick','montaParametrosGET( \'AlterarVeiculoPublicacao\', \'hdnDataEdital,hdnId, veiculoPublicacao, hdnNomeVeiculo, dataPublicacao, observacao, inNumPublicacao\')');";

    return $stJs;
}

/*
* Executa alteracao de veículo no array de sessao
*/
function executaAlteracao($observacao)
{
    $arVeiculos = Sessao::read('arVeiculos');

    $numOrdem = Sessao::read('numOrdem');
    //altera a observacao na sessao
    $arVeiculos[$numOrdem]['observacao'] = $observacao;
    Sessao::remove('alterandoTransf6');
    //O formulario volta a ser de inclusao
    $stJs='';
    $stJs.="el = document.getElementById('veiculoPublicacao'); ";
    $stJs.="el.disabled = false; ";
    $stJs.="el = document.getElementById('dataPublicacao'); ";
    $stJs.="el.disabled = false; ";

    $stJs.= "document.frm.btLimparVeiculoPublicacao.disable = false; ";

    $stJs.= "document.frm.btIncluirVeiculoPublicacao.value='Incluir'; ";
    //atualiza lista
    $stJs.=montaListaVeiculosPublicacao( $arVeiculos );

    return $stJs;
}

/*
* Monta lista com os veiculos e alterar/excluir
*/
function montaListaVeiculosPublicacao($arRecordSet , $boExecuta = true)
{
    //global $pgOcul;
    $rs = new RecordSet();
    $rs->preenche($arRecordSet);

    $obListaVeiculos = new Lista;
    $obListaVeiculos->setMostraPaginacao( true );
    $obListaVeiculos->setRecordSet( $rs );

    $obListaVeiculos->addCabecalho();
    $obListaVeiculos->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaVeiculos->ultimoCabecalho->setWidth( 5 );
    $obListaVeiculos->commitCabecalho();

    $obListaVeiculos->addCabecalho();
    $obListaVeiculos->ultimoCabecalho->addConteudo("Veículos de Publicação");
    $obListaVeiculos->ultimoCabecalho->setWidth( 15);
    $obListaVeiculos->commitCabecalho();

    $obListaVeiculos->addCabecalho();
    $obListaVeiculos->ultimoCabecalho->addConteudo("Data");
    $obListaVeiculos->ultimoCabecalho->setWidth( 20 );
    $obListaVeiculos->commitCabecalho();
    
    $obListaVeiculos->addCabecalho();
    $obListaVeiculos->ultimoCabecalho->addConteudo("Número da Publicação");
    $obListaVeiculos->ultimoCabecalho->setWidth( 20 );
    $obListaVeiculos->commitCabecalho();

    $obListaVeiculos->addCabecalho();
    $obListaVeiculos->ultimoCabecalho->addConteudo("Observacao");
    $obListaVeiculos->ultimoCabecalho->setWidth( 20);
    $obListaVeiculos->commitCabecalho();

    $obListaVeiculos->addCabecalho();
    $obListaVeiculos->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaVeiculos->ultimoCabecalho->setWidth( 5 );
    $obListaVeiculos->commitCabecalho();

    $obListaVeiculos->addDado();
    $obListaVeiculos->ultimoDado->setCampo( "nomeVeiculoPublicacao" );
    $obListaVeiculos->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaVeiculos->commitDado();

    $obListaVeiculos->addDado();
    $obListaVeiculos->ultimoDado->setCampo( "dataPublicacao" );
    $obListaVeiculos->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaVeiculos->commitDado();

    $obListaVeiculos->addDado();
    $obListaVeiculos->ultimoDado->setCampo( "inNumPublicacao" );
    $obListaVeiculos->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaVeiculos->commitDado();
    
    $obListaVeiculos->addDado();
    $obListaVeiculos->ultimoDado->setCampo( "observacao" );
    $obListaVeiculos->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaVeiculos->commitDado();

    $obListaVeiculos->addAcao();
    $obListaVeiculos->ultimaAcao->setAcao( "ALTERAR" );
    $obListaVeiculos->ultimaAcao->setFuncaoAjax( true );
    $obListaVeiculos->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterarItemListaVeiculo');" );
    $obListaVeiculos->ultimaAcao->addCampo("1","id");
    $obListaVeiculos->commitAcao();

    $obListaVeiculos->addAcao();
    $obListaVeiculos->ultimaAcao->setAcao( "EXCLUIR" );
    $obListaVeiculos->ultimaAcao->setFuncaoAjax( true );
    $obListaVeiculos->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirItemListaVeiculo');" );
    $obListaVeiculos->ultimaAcao->addCampo("1","id");
    $obListaVeiculos->commitAcao();

    $obListaVeiculos->montaHTML();
    $stHTML = $obListaVeiculos->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnListaVeiculosPublicacao').innerHTML = '".$stHTML."';";

        return $stJs;
    } else {
        return $stHTML;
    }
}

function alteraVeiculo()
{
    
    $boIncluir = true;
    $arVeiculos = Sessao::read('arVeiculos');
    Sessao::remove('arVeiculos');

    $stNomeVeiculo = sistemaLegado::pegaDado('nom_cgm','SW_CGM',' where numcgm ='.$_REQUEST['veiculoPublicacao'].' ');

    foreach ($arVeiculos as $indice  => $dados) {
        if ( ($dados['veiculoPublicacao'] == $_REQUEST['veiculoPublicacao']) && ($dados['dataPublicacao'] == $_REQUEST['dataPublicacao']) && ( $_REQUEST['hdnId'] !== (string) $indice )) {
            $boIncluir = false;
            $stMensagem = 'Este veículo já está cadastrado para esta data!';
        }
    }
    if ($boIncluir == true) {
        foreach ($arVeiculos as $indice  => $dados) {
            if ($indice == $_REQUEST['hdnId']) {
                $arVeiculos[$indice]['veiculoPublicacao'] = $_REQUEST['veiculoPublicacao'];
                $arVeiculos[$indice]['nomeVeiculoPublicacao'] = $_REQUEST['veiculoPublicacao']." - ".$stNomeVeiculo;
                $arVeiculos[$indice]['dataPublicacao'] = $_REQUEST['dataPublicacao'];
                $arVeiculos[$indice]['observacao'] = $_REQUEST['observacao'];
                $arVeiculos[$indice]['inNumPublicacao'] = $_REQUEST['inNumPublicacao'];
            }
        }

        $js .= montaListaVeiculosPublicacao( $arVeiculos );

    } else {
        $js.= "alertaAviso('".$stMensagem."', 'form','','".Sessao::getId()."');";
    }

    Sessao::write('arVeiculos', $arVeiculos);

    $js .= "document.getElementById('veiculoPublicacao').value = '';";
    $js .= "document.getElementById('nomeVeiculoPublicacao').innerHTML = '&nbsp;';";
    $js .= "document.getElementById('dataPublicacao').value = '';";
    $js .= "document.getElementById('observacao').value = '';";
    $js .= "document.getElementById('inNumPublicacao').value = '';";
    $js .= "document.frm.btIncluirVeiculoPublicacao.value='Incluir'; ";
    $js.= "document.frm.btIncluirVeiculoPublicacao.setAttribute('onclick','montaParametrosGET( \'incluirVeiculoPublicacao\', \'hdnDataEdital, veiculoPublicacao, nomeVeiculoPublicacao, dataPublicacao, observacao, inNumPublicacao\')');";

    return $js;
}

switch ($stCtrl) {

    case 'incluirVeiculoPublicacao':

            $boIncluir = true;
            $arVeiculos = Sessao::read('arVeiculos');

            foreach ($arVeiculos as $arVeiculo) {
                if ( ($arVeiculo['veiculoPublicacao'] == $_REQUEST['veiculoPublicacao']) AND ($arVeiculo['dataPublicacao'] == $_REQUEST['dataPublicacao']) AND ( $_REQUEST['hdnId'] !== (string) $arVeiculo['id'] ) ) {
                    $boIncluir = false;
                    $stMensagem = 'Este veículo já está cadastrado para esta data.';
                }
            }

            if ($_REQUEST['veiculoPublicacao'] == "") {
                $boIncluir = false;
                $stMensagem = 'Campo Veículo de Publicação inválido.';
            }

            if ( implode(array_reverse(explode('/',$_REQUEST['hdnDataEdital']))) > implode(array_reverse(explode('/',$_REQUEST['dataPublicacao']))) ) {
                $boIncluir = false;
                $stMensagem = 'A data de publicação é inferior a data de aprovação do edital.';
            }
            
            if ($boIncluir) {
                if ($_REQUEST['hdnId'] != '') {
                    $id = $_REQUEST['hdnId'];
                } else {
                    $id = count($arVeiculos);
                    $arVeiculos[$id]['id'] = $id;
                }
                $arVeiculos[$id]['veiculoPublicacao'] = (int) $_REQUEST['veiculoPublicacao'];
                if ($_REQUEST['hdnNomeVeiculo'] == '') {
                    $arVeiculos[$id]['nomeVeiculoPublicacao'] = $_REQUEST['veiculoPublicacao']." - ".$_REQUEST['nomeVeiculoPublicacao'];
                } else {
                    $arVeiculos[$id]['nomeVeiculoPublicacao'] = $_REQUEST['veiculoPublicacao']." - ".$_REQUEST['hdnNomeVeiculo'];
                }
                $arVeiculos[$id]['dataPublicacao'] = $_REQUEST['dataPublicacao'];
                $arVeiculos[$id]['observacao'] = ( $_REQUEST['observacao'] != '' ) ? htmlspecialchars($_REQUEST['observacao'],ENT_QUOTES) : ' ';
                $arVeiculos[$id]['inNumPublicacao'] = $_REQUEST['inNumPublicacao'];
                $stJs .= "document.getElementById('hdnId').value = ''; ";
                $stJs .= "document.getElementById('hdnNomeVeiculo').value = ''; ";

                Sessao::write('arVeiculos', $arVeiculos);
                $stJs .= montaListaVeiculosPublicacao( $arVeiculos );
            } else {
                $stJs.= "alertaAviso('".$stMensagem."', 'form','','".Sessao::getId()."');";
            }

            $stJs .= "document.getElementById('veiculoPublicacao').value = '';";
            $stJs .= "document.getElementById('nomeVeiculoPublicacao').innerHTML = '&nbsp;';";
            $stJs .= "document.getElementById('dataPublicacao').value = '';";
            $stJs .= "document.getElementById('observacao').value = '';";
            $stJs .= "document.getElementById('inNumPublicacao').value = '';";
            $stJs .= "document.frm.btIncluirVeiculoPublicacao.value='Incluir'; ";
        break;

    case 'exibeEdital':

            if (empty($_REQUEST['numEdital'])) {
                $stJs .= "document.getElementById('spnLicitacao').innerHTML = ''; ";
                $stJs .= "document.getElementById('spnListaVeiculosPublicacao').innerHTML = ''; ";
                $stJs .= "document.getElementById('numEdital').focus();";
                break;
            }
            //por via das dúvidas, limpa sessao
            Sessao::remove('alterandoTransf6');
            //unset(sessao->transf6);
            Sessao::remove('arVeiculos');
            $arEdital = explode('/',$_REQUEST['numEdital']);
            $arEdital[1] = ($arEdital[1] == '') ? Sessao::getExercicio() : $arEdital[1];

            //O usuario digitou algo, agora busca no BD
            $obj = new TLicitacaoEdital;
            $obj->setDado("num_edital",$arEdital[0]);
            $obj->setDado('exercicio',$arEdital[1]);
            $obj->recuperaLicitacao($rs);

            //edital nao encontrado
            if ($arEdital[1] == Sessao::getExercicio()) {
                if ($rs->eof()) {
                    //Aviso de que o edital nao existe
                    $stJs .= "alertaAviso('Edital ".$arEdital[0]."/".$arEdital[1]." não encontrado!', 'form','erro','".Sessao::getId()."');";
                    //Limpa o span relacionado a este campo
                    $stJs .= "document.getElementById('spnLicitacao').innerHTML = ''; ";
                    //LIMPA o html no span de inclusao de veiculo de publicacao
                    $stJs .= "document.getElementById('spnListaVeiculosPublicacao').innerHTML = ''; ";
                    //LIMPA o campo edital
                    $stJs .= "document.getElementById('numEdital').value = '';\n";
                    //Foco novamente no campo para ser digitado
                    $stJs .= "document.getElementById('numEdital').focus(); ";
                    break;
                }
            } else {
            $stJs .= "alertaAviso('Edital ".$arEdital[0]."/".$arEdital[1]." inválido!', 'form','erro','".Sessao::getId()."');";
            $stJs .= "document.getElementById('spnLicitacao').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnListaVeiculosPublicacao').innerHTML = ''; ";
            $stJs .= "document.getElementById('numEdital').value = '';";
            $stJs .= "document.getElementById('numEdital').focus(); ";
            break;
        }
        //edital encontrado, mostra os dados da licitação no span
        //Monta  objetos no span de dados de publicação do edital
        //Numero da licitação

        $labelNumLicitacao = new Label();
        $labelNumLicitacao->setId( 'numLicitacao' );
        $labelNumLicitacao->setValue($rs->getCampo('cod_licitacao').'/'.$rs->getCampo('exercicio'));
        $labelNumLicitacao->setRotulo( 'Número da Licitação' );

        $labelModalidade = new Label();
        $labelModalidade->setId( 'numModalidade' );
        $labelModalidade->setValue($rs->getCampo('cod_modalidade').' - '.$rs->getCampo('modalidade_descricao'));
        $labelModalidade->setRotulo( 'Modalidade' );

        $labelEntidade = new Label();
        $labelEntidade->setId('numEntidade');
        $labelEntidade->setValue($rs->getCampo('cod_entidade').' - '.$rs->getCampo('nom_cgm'));
        $labelEntidade->setRotulo( 'Entidade' );
        //Objeto do processo licitatorio
        $labelObjetoProcLic = new ILabelEditObjeto();
        $labelObjetoProcLic->setCodObjeto($rs->getCampo('cod_objeto'));
        $labelObjetoProcLic->setName( 'objeto' );
        $labelObjetoProcLic->setRotulo( 'Objeto' );
        //label para a data de aprovação do jurídico;
        $labelDataAprovacao = new Label();
        $labelDataAprovacao->setRotulo( 'Data de Aprovação do Jurídico' );
        $labelDataAprovacao->setValue( $rs->getCampo('dt_aprovacao_juridico') );
        //Hidden para armazenar o exercicio do edital
        $obExercicio = new Hidden;
        $obExercicio->setName( "exercicioEdital" );
        $obExercicio->setValue( $rs->getCampo('exercicio') );

        //Hidden para armazenar a data de aprovação do edital
        $obHdnDataEdital = new Hidden();
        $obHdnDataEdital->setName( 'hdnDataEdital' );
        $obHdnDataEdital->setValue( $rs->getCampo('dt_aprovacao_juridico') );

        $obHdnId = new Hidden();
        $obHdnId->setId( 'hdnId' );
        $obHdnId->setName( 'hdnId' );
        $obHdnId->setValue( '' );

        /* Objetos de 'Veiculo de publicacao' */
        include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

        //$obForm  = unserialize(sessao->transf3['componente']);
        $obForm = Sessao::read('componente');
        $obVeiculoPublicacao = new IPopUpCGMVinculado( $obForm );
        $obVeiculoPublicacao->setTabelaVinculo ( 'licitacao.veiculos_publicidade' );
        $obVeiculoPublicacao->setCampoVinculo ( 'numcgm' );
        $obVeiculoPublicacao->setNomeVinculo ( 'Veículo de Publicação' );
        $obVeiculoPublicacao->setRotulo ( 'Veículo de Publicação' );
        $obVeiculoPublicacao->setTitle ( 'Informe o CGM do veículo de publicação.' );
        $obVeiculoPublicacao->setName ( 'nomeVeiculoPublicacao');
        $obVeiculoPublicacao->setId ( 'nomeVeiculoPublicacao');
        $obVeiculoPublicacao->obCampoCod->setName ( 'veiculoPublicacao' );
        $obVeiculoPublicacao->obCampoCod->setId ( 'veiculoPublicacao' );
        $obVeiculoPublicacao->obCampoCod->setNull ( true );
        $obVeiculoPublicacao->setNull ( true );
        $obVeiculoPublicacao->setObrigatorioBarra(true);

        //data da publicação
        $obDataPublicacao = new Data;
        $obDataPublicacao->setName  ( "dataPublicacao" );
        $obDataPublicacao->setRotulo( "Data da Publicação" );
        $obDataPublicacao->setTitle ( "Informe a data de publicação do edital." );
        $obDataPublicacao->setId("dataPublicacao");
        $obDataPublicacao->setNull ( true );
        $obDataPublicacao->setObrigatorioBarra(true);
        //observacao
        $obObservacao = new TextBox;
        $obObservacao->setName  ( "observacao" );
        $obObservacao->setRotulo( "Observação" );
        $obObservacao->setTitle ( "Informe uma breve observação da publicação." );
        $obObservacao->setId("observacao");
        $obObservacao->setSize(80);
        $obObservacao->setMaxLength(80);
        $obObservacao->setNull ( true);

        $obBtnIncluir = new Button;
        $obBtnIncluir->setName ( "btIncluirVeiculoPublicacao" );
        $obBtnIncluir->setValue( "Incluir" );
        $obBtnIncluir->setTipo ( "button" );
        $obBtnIncluir->setStyle( "width: 60px" );
        $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET( 'incluirVeiculoPublicacao', 'hdnDataEdital, veiculoPublicacao, nomeVeiculoPublicacao, dataPublicacao, observacao' );" );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName ( "btLimparVeiculoPublicacao" );
        $obBtnLimpar->setValue( "Limpar" );
        $obBtnLimpar->setTipo ( "button" );
        $obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparListaVeiculoPublicacao');" );

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnDataEdital);
        $obFormulario->addHidden($obHdnId);
        $obFormulario->addComponente($labelEntidade);
        $obFormulario->addComponente($labelNumLicitacao);
        $obFormulario->addComponente($labelModalidade);
        $obFormulario->addComponente($labelObjetoProcLic);
        $obFormulario->addComponente($labelDataAprovacao);
        $obFormulario->addHidden($obExercicio);

        $obFormulario->addTitulo        ( "Veículo de Publicação"          );
        $obFormulario->addComponente    ( $obVeiculoPublicacao           );
        $obFormulario->addComponente    ( $obDataPublicacao              );
        $obFormulario->addComponente    ( $obObservacao                  );
        $obFormulario->defineBarra( array ( $obBtnIncluir , $obBtnLimpar ),"","" );
        $obFormulario->addTitulo        ( "Veículos de Publicação Utilizados"  );

        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
        //Joga o html no span de dados de publicação do edital
        $stJs .= "document.getElementById('numEdital').value = '".$arEdital[0]."/".$arEdital[1]."';";
        $stJs .= "document.getElementById('spnLicitacao').innerHTML = '".$stHTML."'; ";

        //  INICIALIZA A LISTA A PARTIR DO CONTEUDO DO BD

        $objMapeamento = new TLicitacaoPublicacaoEdital;
        $objMapeamento->setDado("num_edital",$arEdital[0]);
        $objMapeamento->setDado('exercicio',Sessao::getExercicio());
        $objMapeamento->recuperaVeiculosPublicacao($rsVeiculos);
        $i=0;

        $arVeiculos = array();
        while (!$rsVeiculos->eof()) {
            $arVeiculos[$i]['veiculoPublicacao'] = $rsVeiculos->getCampo('veiculopublicacao');
            $arVeiculos[$i]['nomeVeiculoPublicacao'] = $rsVeiculos->getCampo('veiculopublicacao')." - ".$rsVeiculos->getCampo('nomeveiculopublicacao');
            $arVeiculos[$i]['dataPublicacao'] = $rsVeiculos->getCampo('datapublicacao');
            $arVeiculos[$i]['observacao'] = $rsVeiculos->getCampo('observacao');
            $arVeiculos[$i]['inNumPublicacao'] = $rsVeiculos->getCampo('num_publicacao');
            $arVeiculos[$i]['id'] = $i;
            $rsVeiculos->proximo();
            $i++;
        }
        $stJs.= montaListaVeiculosPublicacao($arVeiculos);
        Sessao::write('arVeiculos', $arVeiculos);

    break;

    case 'montaListaVeiculos':
        $arVeiculos = Sessao::read('arVeiculos');
        $stJs.= montaListaVeiculosPublicacao($arVeiculos);
    break;

    case 'alterarItemListaVeiculo':
        $stJs.= montaAlteracao((int) $_REQUEST['id']);
    break;

    case 'AlterarVeiculoPublicacao':
        $stJs.= alteraVeiculo();
    break;

    case 'excluirItemListaVeiculo':
        //varre os elementos do array e descarta o elemento desnecessário
        $arTemp = array();
        $novoNumOrdem=0;
        $arVeiculos = Sessao::read('arVeiculos');
        $inTotalVeiculos = count($arVeiculos);
        for ($i=0;$i<$inTotalVeiculos;$i++) {
            $item = $arVeiculos[$i];
            if ($item['id']!=(int) $_REQUEST['id']) {
                $item['id']=$novoNumOrdem;
                $arTemp[]=$item;
                $novoNumOrdem++;
            }
        }
        Sessao::write('arVeiculos', $arTemp);
        $stJs .= montaListaVeiculosPublicacao( $arTemp );
    break;

    case 'limparListaVeiculoPublicacao':
        $stJs .= "document.getElementById('veiculoPublicacao').value = '';";
        $stJs .= "document.getElementById('nomeVeiculoPublicacao').innerHTML = '&nbsp;';";
        $stJs .= "document.getElementById('dataPublicacao').value = '';";
        $stJs .= "document.getElementById('observacao').value = '';";
        $stJs .= "document.frm.btIncluirVeiculoPublicacao.value='Incluir'; ";
        $stJs .= "document.frm.btIncluirVeiculoPublicacao.setAttribute('onclick','montaParametrosGET( \'incluirVeiculoPublicacao\', \'hdnDataEdital, veiculoPublicacao, nomeVeiculoPublicacao, dataPublicacao, observacao\')');";
    break;
}

echo $stJs;
?>
