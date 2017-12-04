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
    * Formulario de Convenio
    * Data de Criação   : 03/10/2006

    * @author Analista:
    * @author Desenvolvedor:  Lucas Teixeira Stephanou
    * @ignore

    $Id: OCManterConvenios.php 59803 2014-09-11 20:53:06Z lisiane $

    *Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoPublicacaoConvenio.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenios";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_LIC_INSTANCIAS."convenios/";

function somaParticipacao($rsP)
{
    $nuSoma = 0.00;
    while ( !$rsP->eof() ) {
        $nuSoma += $rsP->getCampo ( 'nuPercentualParticipacao' );
        $rsP->proximo();
    }

    return $nuSoma;
}

function somaValorParticipantes($rs)
{
    $nuSoma = 0.00;
    while ( !$rs->eof() ) {
        $nuSoma += $rs->getCampo ( 'nuValorParticipacao' );
        $rs->proximo();
    }

    return $nuSoma;
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
        $table->Head->addCabecalho( 'Data', 10  );
        $table->Head->addCabecalho( 'Número Publicação', 12  );
        $table->Head->addCabecalho( 'Observação'     , 40  );

        $table->Body->addCampo( '[inVeiculo]-[stVeiculo] ' , 'E');
        $table->Body->addCampo( 'dtDataPublicacao' );
        $table->Body->addCampo( 'inNumPublicacao' );
        $table->Body->addCampo( '_stObservacao'  );

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

function montaListaParticipantes($rsLista , $stJs = null)
{
    if ( $rsLista->getNumLinhas() > 0 ) {

        //$rsLista->addFormatacao( 'nuPercentualParticipacao', 'NUMERIC_BR' );
        $rsLista->addFormatacao( 'nuValorParticipacao', 'NUMERIC_BR' );

        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Participantes do Convênio " );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Nome" );
        $obLista->ultimoCabecalho->setWidth ( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Tipo de Participação" );
        $obLista->ultimoCabecalho->setWidth ( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo ( "Função" );
        $obLista->ultimoCabecalho->setWidth ( 40 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Valor Participação" );
        $obLista->ultimoCabecalho->setWidth ( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Participação " );
        $obLista->ultimoCabecalho->setWidth ( 60 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stNomCgmParticipante" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "descricao_participacao" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "stFuncaoParticipante" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento ( "CENTRO" );
        $obLista->ultimoDado->setCampo ( "nuValorParticipacao" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento ( "CENTRO" );
        $obLista->ultimoDado->setCampo ( "[nuPercentualParticipacao] %" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:alterarParticipante();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","inCgmParticipante" );
        $obLista->commitAcao ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:excluirParticipante();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","inCgmParticipante" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnParticipantes').innerHTML = '".$stHTML."';\n";
    if ( $stJs )
        $js .= $stJs;

    return $js;
}

function montaListaAlteracao($rsLista , $stJs = null , $stAcao = 'alterar')
{
    global $stCaminho;
    global $pgProc;

    $rsLista->addFormatacao ( 'valor' , 'NUMERIC_BR' );

    $rsLista->setPrimeiroElemento();
    $obLista = new Lista;
    $obLista->setRecordSet ( $rsLista );
    $obLista->setTitulo ( "Resultados da Busca" );
    $obLista->setMostraPaginacao ( false );

    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth    ( 5 );
    $obLista->commitCabecalho ();

    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo ( "Número do Convênio" );
    $obLista->ultimoCabecalho->setWidth ( 10 );
    $obLista->commitCabecalho ();

    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo ( "Tipo do Convênio" );
    $obLista->ultimoCabecalho->setWidth ( 30);
    $obLista->commitCabecalho ();

    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo ( "Objeto do Convênio" );
    $obLista->ultimoCabecalho->setWidth ( 40 );
    $obLista->commitCabecalho ();

    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo ( "Valor" );
    $obLista->ultimoCabecalho->setWidth ( 15 );
    $obLista->commitCabecalho ();

    $obLista->addCabecalho ();
    $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth ( 5 );
    $obLista->commitCabecalho ();

    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento ( "CENTRO" );
    $obLista->ultimoDado->setCampo ( "num_convenio" );
    $obLista->commitDado ();

    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento ( "CENTRO" );
    $obLista->ultimoDado->setCampo ( "descricao_tipo" );
    $obLista->commitDado ();

    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento ( "CENTRO" );
    $obLista->ultimoDado->setCampo ( "descricao_objeto" );
    $obLista->commitDado ();

    $obLista->addDado ();
    $obLista->ultimoDado->setAlinhamento ( "CENTRO" );
    $obLista->ultimoDado->setCampo ( "valor" );
    $obLista->commitDado ();

    if ($stAcao == 'alterar') {
        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:alterarConvenio();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","num_convenio" );
        $obLista->ultimaAcao->addCampo("&inExercicio"   , "exercicio"    );
        $obLista->commitAcao ();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( 'excluir' );
        $obLista->ultimaAcao->addCampo("&inNumConvenio" , "num_convenio" );
        $obLista->ultimaAcao->addCampo("&stDescQuestao" , "num_convenio" );
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&nomAcao=Excluir%20Convênio&stAcao=excluirConvenio" );
        $obLista->commitAcao();

    } elseif ($stAcao == 'consultar') {
        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "consultar" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:consultarConvenio();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","num_convenio" );
        $obLista->commitAcao ();

    } elseif ($stAcao == 'anular') {
        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "anular" );
        $obLista->ultimaAcao->setFuncao ( true );
        $obLista->ultimaAcao->setLink ( "JavaScript:anularConvenio();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","num_convenio" );
        $obLista->commitAcao ();
    }

    $obLista->montaHTML ();
    $stHTML =  $obLista->getHtml ();
    $stHTML = str_replace ( "\n","",$stHTML );
    $stHTML = str_replace ( "  ","",$stHTML );
    $stHTML = str_replace ( "'","\\'",$stHTML );

    $js = "d.getElementById('spnResultado').innerHTML = '".$stHTML."';\n";
    if ( $stJs )
        $js .= $stJs;

    return $js;
}

/*
    Se estiver setada a data de publicacao da rescisao e a observação da publicacao, quer dizer que
    está rescindindo o contrato, com isso $boRescindir recebe o valor TRUE
*/
$boRescindir = false;
if ( (isset($_REQUEST["dtPublicacaoRescisao"])) && (isset($_REQUEST["stObsPublicacao"]))) {
    $boRescindir = true;
}

$inNumConvenio = $request->get('inNumConvenio');

switch ($request->get('stCtrl')) {

    case 'LimparSessao' :
        Sessao::remove('participantes');
        Sessao::remove('rsVeiculos');
        break;

    case 'IncluirVeiculo' :
        $boFoco = false;
        $rsVeiculos = Sessao::read('rsVeiculos');
        $inNumCgmVeiculoPublicidade = $_REQUEST[ 'inCgmVeiculoPublicidade' ];
        if ($inNumCgmVeiculoPublicidade != "") {
            if ( ($boRescindir) && ($_REQUEST["dtPublicacaoRescisao"] == "") ) {
                $stErro = "@Data de Publicação não informada!";
                $boFoco = true;
            } else {
                if ($rsVeiculos ==  null) {
                    $rsVeiculos = new Recordset;
                } else {
                    while ( !$rsVeiculos->eof() ) {
                        if ( $rsVeiculos->getCampo('inCgmVeiculoPublicidade') == $inNumCgmVeiculoPublicidade ) {
                            $stErro = "@Veiculo de Publicidade ja incluido!";
                        }
                        $rsVeiculos->proximo();
                    }
                    $rsVeiculos->setPrimeiroElemento();
                }
            }
        } else {
            $stErro = "@Veículo de Publicidade deve ser informado!";
            $boFoco = true;
        }
        if ($stErro) {
            echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
            if ( $boFoco ) echo " setTimeout('document.getElementById(\'inCgmVeiculoPublicidade\').focus();',400);\n";
        } else {
            // limpar campos
            $stJs  = "d.getElementById('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;'; \n";
            $stJs .= "f.inCgmVeiculoPublicidade.value = '' ;\n";

            /* campos recisão convenio */
            $stJs .= "var dtPublicacaoRescisao = d.getElementById('dtPublicacaoRescisao');";
            $stJs .= "if ( dtPublicacaoRescisao ) dtPublicacaoRescisao.value = '';";
            $stJs .= "var stObsPublicacao = d.getElementById('stObsPublicacao');";
            $stJs .= "if ( stObsPublicacao ) stObsPublicacao.value = '';";
            $stJs .= "var dtRescisao = d.getElementById('dtRescisao');";
            $stJs .= "if ( dtRescisao ) dtRescisao.readOnly = true;\n";

            // buscar cgm
            require_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php");
            $obCgm = new RCGM;
            $obCgm->setNumCGM ( $inNumCgmVeiculoPublicidade );
            $obCgm->consultar ( new Recordset );
            $stNomCgm = $obCgm->getNomCGM();
            unset ( $obCgm );

            // cria array a ser inserido
            if ($boRescindir) {
                $arVeiculo = array  (
                                    'inCgmVeiculoPublicidade' => $inNumCgmVeiculoPublicidade ,
                                    'stNomCgmVeiculoPublicadade' => $stNomCgm ,
                                    'dtPublicacaoRescisao' => $_REQUEST["dtPublicacaoRescisao"] ,
                                    'stObsPublicacao' => $_REQUEST["stObsPublicacao"]
                                );

            } else {
                $arVeiculo = array  (
                                    'inCgmVeiculoPublicidade' => $inNumCgmVeiculoPublicidade ,
                                    'stNomCgmVeiculoPublicadade' => $stNomCgm
                                );
            }

            $rsVeiculos->add ( $arVeiculo );
            $rsVeiculos->ordena ( 'stNomCgmVeiculoPublicadade' );
            Sessao::write('rsVeiculos',$rsVeiculos);
            echo montaListaVeiculos (  $rsVeiculos , $stJs, $boRescindir);

        }
        break;

    case 'limpaVeiculo' :
        $stJs  = "d.getElementById('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;'; \n";
        $stJs .= "f.inCgmVeiculoPublicidade.value = '';";
        $stJs .=  "\n var dtPublicacaoRescisao = d.getElementById('dtPublicacaoRescisao');"
                 ."\n if ( dtPublicacaoRescisao ) dtPublicacaoRescisao.value = '';"
                 ."\n var stObsPublicacao = d.getElementById('stObsPublicacao');"
                 ."\n if ( stObsPublicacao ) stObsPublicacao.value = '';";
        echo $stJs;
        break;

    case 'excluirVeiculo':
        $rsVeiculos = Sessao::read('rsVeiculos');
        $arVeiculos = $rsVeiculos->arElementos;
        $arNovo = array();
        $numcgmExcluir = $_REQUEST['inCgmVeiculoPublicidade'];
        foreach ($arVeiculos as $valor) {
            if ($valor[ 'inCgmVeiculoPublicidade' ] != $numcgmExcluir) {
                $arNovo[] = $valor;
            }
        }

        /*
            Libera o campo, pois quando a acao é rescindir, há o campo data de publicacao
            nos dados a serem adicionados na lista de veículos de publicidade.
            Após a primeira insersão na listagem, o campo data de rescisão é
            passado para readOnly, assim a data não pode ser mais alterada, não tendo
            problemas de alteração de data. O campo é volta ao normal quando a listagem
            ficar fazia.
        */
        if ( empty($arNovo) ) {
            echo "if (d.getElementById('dtRescisao')) {";
            echo "  d.getElementById('dtRescisao').readOnly = false;";
            echo "}";
        }

        $rsVeiculos = new Recordset;
        $rsVeiculos->preenche ( $arNovo );
        $rsVeiculos->ordena ( 'stNomCgmVeiculoPublicadade' );
        Sessao::write('rsVeiculos',$rsVeiculos);
        echo montaListaVeiculos ( $rsVeiculos );
        break;

    case 'incluirParticipante' :
        $arParticipante = Sessao::read('participantes');
        $inCgmParticipanteEditado = Sessao::read('inCgmParticipanteEditado');
        $stErro = '';

        if ( $request->get('inCgmParticipante') == '' ) {
            $stErro = 'Preencha o CGM do Participante!';
        } elseif ( $request->get('inCodTipoParticipante') == '' ) {
            $stErro = 'Selecione o tipo de Participação!';
        } elseif ( $request->get('nuValorParticipacao') == '' ) {
            $stErro = 'Preencha o Valor de Participação!';
        } elseif ( $request->get('stFuncaoParticipante') == '' ) {
            $stErro = 'Preencha a Função do Participante!';
        }

        $inCgmParticipante          = $request->get('inCgmParticipante');
        $inCodTipoParticipante      = $request->get('inCodTipoParticipante');

        $nuValorParticipacao        = str_replace ( '.' , '' , $request->get('nuValorParticipacao')) ;
        $nuValorParticipacao        = str_replace ( ',' , '.' , $nuValorParticipacao ) ;

        $nuPercentualParticipacao   = $request->get('hdnPercentualParticipacao');
        $hdnPercentualParticipacao  = $request->get('hdnPercentualParticipacao');

        $nuValorConvenio            = str_replace ( '.' , '' , $request->get('nuValorConvenio') );
        $nuValorConvenio            = str_replace ( ',' , '.' , $nuValorConvenio );

        $nuSomaParticipantes        = $arParticipante ? somaValorParticipantes( $arParticipante ) : 0.00 ;

        $stFuncao                   = $request->get('stFuncaoParticipante');

        $boAlteracao  = Sessao::read('boAlteracao');
        $nuValorAtual = Sessao::read('nuValorAtual');

        if ($boAlteracao == true) {
            $nuValorAtualTMP = str_replace ( ',' , '.' , $nuValorAtual) ;
            $tmp = number_format($nuValorAtualTMP, 2, '.', '');
            $nuSomaParticipantes = number_format($nuSomaParticipantes, 2, '.', '') - $tmp;
        }

        $valorTotalParticipantes = number_format($nuSomaParticipantes + $nuValorParticipacao,2,'.','');

        if (!$nuValorConvenio) {
            $stErro = "@<i>Valor do Convênio</i> deve ser informado antes da inclusão de participantes!";

/*        } elseif (($valorTotalParticipantes > $nuValorConvenio)&&(!$boAlteracao)) {
            $stErro = "@<i>Valor de Participação</i> somado aos atuais participantes ultrapassa o <i>Valor do Convênio</i>!";
        } else //if (($valorTotalParticipantes < $nuValorConvenio)&&($boAlteracao)) {
            //$stErro = "@<i>Valor de Participação</i> somado aos atuais
            //participantes é menor que o  <i>Valor do Convênio</i>!";        */

        } else {
            if ($arParticipante ==  null) {
                $rsParticipantes = new Recordset;
            } else {
                $rsParticipantes = $arParticipante;
                $rsParticipantes->setPrimeiroElemento();

                while ( !$rsParticipantes->eof() ) {
                    if ( $rsParticipantes->getCampo('inCgmParticipante') == $inCgmParticipante ) {
                        if ( $boAlteracao != true )
                            $stErro = "@Participação do CGM ja incluida!";
                    }
                    $rsParticipantes->proximo();
                }
                $rsParticipantes->setPrimeiroElemento();
            }
        }

        if ($stErro != '') {
            echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
            echo " setTimeout('document.getElementById(\'inCgmParticipante\').focus();',400);\n";
        } else {
            // limpar campos
            $stJs  = "d.getElementById('stNomCgmParticipante').innerHTML = '&nbsp;'; \n";
            $stJs .= "d.getElementById('nuValorParticipacao').value = '0,00'; \n";
            $stJs .= "d.getElementById('hdnPercentualParticipacao').value = ''; \n";
            $stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML = '0,00 %'; \n";
            $stJs .= "d.getElementById('inCodTipoParticipante').value = ''\n";
            $stJs .= "d.getElementById('stFuncaoParticipante').value = ''\n";
            $stJs .= "f.inCgmParticipante.value = '' ;\n";
            $stJs .= "$('btnIncluirParticipante').value    ='Incluir';   ";

            // buscar cgm
            require_once ( CAM_GA_CGM_NEGOCIO . "RCGM.class.php");
            $obCgm = new RCGM;
            $obCgm->setNumCGM ( $inCgmParticipante );
            $obCgm->consultar ( new Recordset );
            $stNomCgm = $obCgm->getNomCGM();
            unset ( $obCgm );

            // buscar tipo de participação
            require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoTipoParticipante.class.php");
            $obTLicitacaoTipoParticipante = new TLicitacaoTipoParticipante;
            $obTLicitacaoTipoParticipante->setDado ( 'cod_tipo_participante' , $inCodTipoParticipante );
            $obTLicitacaoTipoParticipante->recuperaPorChave ( $rsTiposParticipante );
            $stDescricaoParticipacao = $rsTiposParticipante->getCampo ( 'descricao' );
            unset ( $obTLicitacaoTipoParticipante );

            // cria array a ser inserido
            $arParticipante = array  (
                                    'inCgmParticipante'         => $inCgmParticipante,
                                    'inCodTipoParticipante'     => $inCodTipoParticipante,
                                    'stNomCgmParticipante'      => $stNomCgm,
                                    'descricao_participacao'    => $stDescricaoParticipacao,
                                    'nuValorParticipacao'       => $nuValorParticipacao,
                                    'nuPercentualParticipacao'  => $nuPercentualParticipacao,
                                    'hdnPercentualParticipacao' => $hdnPercentualParticipacao,
                                    'stFuncaoParticipante'      => $stFuncao
                                    );
            if ($boAlteracao == true) {
                while (!$rsParticipantes->eof()) {
                    if ($rsParticipantes->getCampo( 'inCgmParticipante') != $inCgmParticipanteEditado) {
                        if ($rsParticipantes->getCampo( 'inCgmParticipante') == $inCgmParticipante) {
                            $stErro = "@Participação do CGM ja existe!";
                            break;
                        }
                    }
                    $rsParticipantes->proximo();
                }
                if ($stErro == '') {
                    $rsParticipantes->setPrimeiroElemento();
                    while (!$rsParticipantes->eof()) {
                        if ($rsParticipantes->getCampo( 'inCgmParticipante') == $inCgmParticipanteEditado) {
                            $rsParticipantes->setCampo( 'inCgmParticipante'         , $arParticipante['inCgmParticipante']         );
                            $rsParticipantes->setCampo( 'inCodTipoParticipante'     , $arParticipante['inCodTipoParticipante']     );
                            $rsParticipantes->setCampo( 'stNomCgmParticipante'      , $arParticipante['stNomCgmParticipante']      );
                            $rsParticipantes->setCampo( 'descricao_participacao'    , $arParticipante['descricao_participacao']    );
                            $rsParticipantes->setCampo( 'nuValorParticipacao'       , $arParticipante['nuValorParticipacao']       );
                            $rsParticipantes->setCampo( 'nuPercentualParticipacao'  , str_replace('.',',',$arParticipante['nuPercentualParticipacao'])  );
                            $rsParticipantes->setCampo( 'hdnPercentualParticipacao' , $arParticipante['hdnPercentualParticipacao'] );
                            $rsParticipantes->setCampo( 'stFuncaoParticipante'      , $arParticipante['stFuncaoParticipante']      );
                            break;
                        }
                    $rsParticipantes->proximo();
                    }
                } else {
                     echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
                }
            } else {
                $rsParticipantes->add( $arParticipante );
            }

            Sessao::remove('boAlteracao');
            Sessao::remove('nuValorAtual');
            Sessao::remove('nuPercentualAtual');
            $rsParticipantes->setPrimeiroElemento();
            Sessao::write('participantes',$rsParticipantes);
            echo montaListaParticipantes (  $rsParticipantes , $stJs);
        }
        break;

    case 'limpaParticipante' :
        Sessao::remove('boAlteracao');
        Sessao::remove('nuValorAtual');
        Sessao::remove('nuPercentualAtual');
        $stJs  = "d.getElementById('stNomCgmParticipante').innerHTML = '&nbsp;'; \n";
        $stJs .= "d.getElementById('nuValorParticipacao').value = '0,00'; \n";
        $stJs .= "d.getElementById('hdnPercentualParticipacao').value = ''; \n";
        $stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML = '0,00 %'; \n";
        $stJs .= "d.getElementById('inCodTipoParticipante').value = ''\n";
        $stJs .= "d.getElementById('stFuncaoParticipante').value  = ''\n";
        $stJs .= "f.inCgmParticipante.value = '' ;\n";
        echo $stJs;
        break;

    case 'excluirParticipante':
        Sessao::remove('boAlteracao');
        Sessao::remove('nuValorAtual');
        Sessao::remove('nuPercentualAtual');
        $arParticipante = Sessao::read('participantes');
        $arParticipante = $arParticipante->arElementos;
        $arNovo = array();
        $numcgmExcluir = $_REQUEST['inCgmParticipante'];
        foreach ($arParticipante as $valor) {
            if ($valor[ 'inCgmParticipante' ] != $numcgmExcluir) {
                $arNovo[] = $valor;
            }
        }
        $rsParticipantes = new Recordset;
        $rsParticipantes->preenche ( $arNovo );
        Sessao::write('participantes',$rsParticipantes);
        echo montaListaParticipantes ( $rsParticipantes );
        break;

    case 'atualizaParticipacao' :
            $focoConvenio = false;
            $rsParticipantes = Sessao::read('participantes');
            $somaParticipacoes = '';
            $stErro = '';

            if ($rsParticipantes != '') {
                $arParticipantes = $rsParticipantes->arElementos;
            } else {
                $arParticipantes = array();
            }

            if (is_array($arParticipantes) && count($arParticipantes) > 0) {
                foreach ($arParticipantes as $chave => $dadosParticipantes) {
                    $somaParticipacoes += str_replace('.','',$dadosParticipantes['nuValorParticipacao']);
                }
            }

            $nuValorParticipacao = str_replace ( "," , "." , $request->get('nuValorParticipacao') ) ;
            $nuValorConvenio = str_replace ( "," , "." , $request->get('nuValorConvenio') ) ;

            $nuValorParticipacao = trim(str_replace ( "." , "" , $nuValorParticipacao )) ;
            $nuValorConvenio = trim(str_replace ( "." , "" , $nuValorConvenio )) ;

            if ($nuValorConvenio != '') {
                if ( $nuValorParticipacao > $nuValorConvenio )
                    $stErro = "@<i>Valor de Participação</i> não pode ser maior que o <i>Valor do Convênio</i>";
                    $stJs = "d.getElementById('nuValorParticipacao').value = '".$request->get('nuValorConvenio')."'\n";
                if ( $nuValorParticipacao <= 0 )
                    $stErro = "@<i>Valor de Participação</i> deve ser maior que o 0( zero )";
                // calcula percentual
                if ($nuValorConvenio > 0) {
                    $percentual = $nuValorParticipacao * 100 / $nuValorConvenio ;
                }
                // soma participacao total
                if ( count (Sessao::read('participantes')) < 1  )
                    $nuSoma = 0.00;
                else
                    $nuSoma = somaParticipacao( Sessao::read('participantes') );
                if ( Sessao::read('nuPercentualAtual') )
                    $nuSoma -= Sessao::read('nuPercentualAtual');

                $percentual = number_format($percentual,2,'.','.');

                $boAlteracao = Sessao::read('boAlteracao');

                if ($boAlteracao) {
                    $hdnPercentualParticipacao = Sessao::read('nuPercentualAtual');
                    $hdnPercentualParticipacao = 1-($hdnPercentualParticipacao/100);

                    $nuOldValorParticipacao = $nuValorConvenio * $hdnPercentualParticipacao;
                    $somaParticipacoes =  $somaParticipacoes - $nuOldValorParticipacao;

                    $somaParticipacoes = $somaParticipacoes + $nuValorParticipacao;

                } else {
                    $somaParticipacoes = $somaParticipacoes + $nuValorParticipacao;
                }

            } else {
                $stErro = "@<i>Valor do Convênio</i> deve ser setado!";
                $focoConvenio = true;
            }
            if ($stErro == '') {
                $percentual = $nuValorParticipacao * 100 / $nuValorConvenio ;
                $bruto = $percentual;

                $percentual = number_format ( $percentual , 2 , ',' , '.') . " %";

                $percentual = str_replace('.',',',$percentual);

                $bruto      = number_format ( $bruto      , 2 , '.' , '') . "";
                $stJs  = "d.getElementById('hdnPercentualParticipacao').value = '".$bruto."'; \n";
                $stJs  .= "d.getElementById('nuPercentualParticipacao').innerHTML = '".$percentual."'; \n";
                echo $stJs;
            } else {
                $stJs  = "d.getElementById('nuValorParticipacao').value = '0,00'; \n";
                $stJs .= "d.getElementById('hdnPercentualParticipacao').value = ''; \n";
                $stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML = '0,00 %'; \n";
                $stJs .= "d.getElementById('inCodTipoParticipante').value = ''\n";
                if ( $focoConvenio )
                    $stJs .= "f.nuValorConvenio.focus() ;\n";
                else
                    $stJs .= "f.inCgmParticipante.focus() ;\n";
                echo "alertaAviso('" . $stErro . "','form','erro','".Sessao::getId()."');\n";
                echo $stJs ;
            }
        break;

    case 'alterarParticipante':
        // participante
        $inCgmParticipante          = $request->get('inCgmParticipante');
        $inCgmParticipanteEditado          = $request->get('inCgmParticipante');

        // buscar no recordset
        $rsParticipantes = Sessao::read('participantes');

        while ( !$rsParticipantes->eof() ) {
            if ( $rsParticipantes->getCampo( 'inCgmParticipante' ) == $inCgmParticipante  ) {
                $stNomCgmParticipante = $rsParticipantes->getCampo( 'stNomCgmParticipante' );
                $nuValorParticipacao = number_format($rsParticipantes->getCampo( 'nuValorParticipacao' ) , 2 , ',' , '.' );

                $hdnPercentualParticipacao = $rsParticipantes->getCampo( 'hdnPercentualParticipacao' );

                $nuPercentualParticipacao = $rsParticipantes->getCampo( 'nuPercentualParticipacao' );

                $inCodTipoParticipante = $rsParticipantes->getCampo( 'inCodTipoParticipante' );
                $stFuncao              = $rsParticipantes->getCampo( 'stFuncaoParticipante' );
                break;
            }
            $rsParticipantes->proximo();
        }

        $hdnPercentualParticipacao = str_replace('.',',',$hdnPercentualParticipacao);
        $nuPercentualParticipacao = str_replace('.',',',$nuPercentualParticipacao);

        // carregar informações do participante
        $stJs  = "d.getElementById('stNomCgmParticipante').innerHTML = '" . $stNomCgmParticipante . "'; \n";
        $stJs .= "d.getElementById('nuValorParticipacao').value = '" . $nuValorParticipacao . "'; \n";
        $stJs .= "d.getElementById('hdnPercentualParticipacao').value = '" . $hdnPercentualParticipacao . "'; \n";
        $stJs .= "d.getElementById('nuPercentualParticipacao').innerHTML = '" . $nuPercentualParticipacao . " %'; \n";
        $stJs .= "d.getElementById('stFuncaoParticipante').value = '" . $stFuncao . "';\n";
        if ( $inCodTipoParticipante == 1 )
            $stJs .= "d.getElementById('inCodTipoParticipante').options[1].selected = 'selected'; \n";
        if ( $inCodTipoParticipante == 2 )
            $stJs .= "d.getElementById('inCodTipoParticipante').options[2].selected = 'selected'; \n";

        $stJs .= "f.inCgmParticipante.value = '" . $inCgmParticipante . "' ;\n";
        $stJs .= "jq('#btnIncluirParticipante').val('Alterar');   ";
//        $stJs .= "jq('#btnIncluirParticipante').attr('onClick','montaParametrosGET(\'alterarParticipante\',\'\', true);' );";

        // seta na sessao que estamos alterando
        Sessao::write('boAlteracao',true);
        Sessao::write('nuValorAtual',$nuValorParticipacao);
        Sessao::write('nuPercentualAtual',$hdnPercentualParticipacao);
        Sessao::write('inCgmParticipanteEditado',$inCgmParticipanteEditado);
        echo $stJs ;
        break;

    case 'buscarFiltro':
        require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoConvenio.class.php' );
        $obTLicConvenio = new TLicitacaoConvenio;
        $stFiltro  = "";
        $arFiltro = array();
        if ($_REQUEST[ 'inExercicio' ]) {
            $arFiltro['inExercicio'] = $_REQUEST[ 'inExercicio' ];
            $stFiltro .= ' AND convenio.exercicio = ' . $_REQUEST[ 'inExercicio' ] . '';
        }

        if ($_REQUEST[ 'inNumConvenio' ]) {
            $arFiltro['inNumConvenio'] = $_REQUEST[ 'inNumConvenio' ];
            $stFiltro .= ' AND convenio.num_convenio = ' . $_REQUEST[ 'inNumConvenio' ] . '';
        }
        if ($_REQUEST[ 'inCodTipoConvenio' ]) {
            $arFiltro['inCodTipoConvenio'] = $_REQUEST[ 'inCodTipoConvenio' ];
            $stFiltro .= ' AND  convenio.cod_tipo_convenio = ' . $_REQUEST[ 'inCodTipoConvenio' ] . '';
        }
        if ($_REQUEST[ 'stObjeto' ]) {
            $arFiltro['stObjeto'] = $_REQUEST[ 'stObjeto' ];
            $stFiltro .= ' AND convenio.cod_objeto = ' . $_REQUEST[ 'stObjeto' ] . '';
        }
        if ($_REQUEST[ 'inCgmParticipante' ]) {
            $arFiltro['inCgmParticipante'] = $_REQUEST[ 'inCgmParticipante' ];
            $stFiltro .= ' AND participante_convenio.cgm_fornecedor = ' . $_REQUEST[ 'inCgmParticipante' ] . '';
        }
        if ( $_REQUEST['stAcao'] != 'consultar' )
            $stFiltro = ' AND convenio_anulado.num_convenio is null ' . $stFiltro;
        $obTLicConvenio->recuperaRelacionamento ( $rsConvenio , $stFiltro , ' convenio.num_convenio');
        Sessao::write('filtro',$arFiltro);
//      $obTLicConvenio->debug();
        $stJs =  montaListaAlteracao ( $rsConvenio  , '' , $_REQUEST['stAcao'] );
        echo $stJs;
        break;

    case 'montaListas':
        require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoParticipanteConvenio.class.php" );
        require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoTipoParticipante.class.php" );
        require_once ( CAM_GA_CGM_NEGOCIO . "RCGM.class.php");

        $obCgm = new RCGM;

        //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
        $obTLicitacaoPublicacaoConvenio = new TLicitacaoPublicacaoConvenio();
        $obTLicitacaoPublicacaoConvenio->setDado('exercicio', Sessao::getExercicio());
        $obTLicitacaoPublicacaoConvenio->setDado('num_convenio', $_REQUEST['inNumConvenio']);

        $obTLicitacaoPublicacaoConvenio->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );
        $inCount = 0;
        $arValores = array();
        while ( !$rsVeiculosPublicacao->eof() ) {
            $arValores[$inCount]['id'            ]   = $inCount + 1;
            $arValores[$inCount]['inVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
            $arValores[$inCount]['stVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'nom_veiculo' );
            $arValores[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao' );
            $arValores[$inCount]['inNumPublicacao']  = $rsVeiculosPublicacao->getCampo( 'num_publicacao' );
            $arValores[$inCount]['_stObservacao'  ]  = $rsVeiculosPublicacao->getCampo( 'observacao' );
            $inCount++;
            $rsVeiculosPublicacao->proximo();
        }

        Sessao::write('arValores', $arValores);
        $stJs = montaListaVeiculos ( $arValores );

        $obParConvenio = new TLicitacaoParticipanteConvenio;
        $stFiltro 	   = " WHERE num_convenio = " . $inNumConvenio;
        $obParConvenio->recuperaRelacionamento( $rsParticipantes , $stFiltro , '' );

        $obTipoParticipante = new TLicitacaoTipoParticipante;
        $arParticipantes = array();

        // carrega participantes
        while ( !$rsParticipantes->eof() ) {
            $obCgm->setNumCGM ( $rsParticipantes->getCampo( 'cgm_fornecedor' ) );
            $obCgm->consultar ( new Recordset );
            $stNomCgm = $obCgm->getNomCGM();

            $obTipoParticipante->setDado ( 'cod_tipo_participante' , $rsParticipantes->getCampo ( 'cod_tipo_participante' ) );
            $obTipoParticipante->recuperaPorChave ( $rsTipoParticipante );

            $valorParticipacao  = str_replace(',','.',$rsParticipantes->getCampo( 'percentual_participacao' ));

            $participacao = number_format( $valorParticipacao , 2 , ',' , '.');
            $participacao = str_replace('.',',', $valorParticipacao);

            $arParticipantes[]  = array  (
                                'inCgmParticipante'         => $rsParticipantes->getCampo( 'cgm_fornecedor' ) ,
                                'inCodTipoParticipante'     => $rsParticipantes->getCampo ( 'cod_tipo_participante' ) ,
                                'stNomCgmParticipante'      => $stNomCgm ,
                                'descricao_participacao'    => $rsTipoParticipante->getCampo ( 'descricao' ) ,
                                'nuValorParticipacao'       => $rsParticipantes->getCampo( 'valor_participacao' ),
                                'nuPercentualParticipacao'  => $participacao,
                                'hdnPercentualParticipacao' => $rsParticipantes->getCampo( 'percentual_participacao' ),
                                'stFuncaoParticipante'      => $rsParticipantes->getCampo( 'funcao'                  )
                            );

            $rsParticipantes->proximo();
        }

        $rsParticipantes = new Recordset;
        $rsParticipantes->preenche ( $arParticipantes );
        Sessao::write('participantes',$rsParticipantes);
        $stJs .= montaListaParticipantes ( $rsParticipantes );

        echo $stJs;
    break;

    case 'montaBuscaNorma':
         if($_REQUEST['inCodLei'] ){
            include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"  );
            $obTNorma = new TNorma;
            $obTNorma->setDado('cod_norma' , $_REQUEST['inCodLei']);
            $obTNorma->recuperaPorChave($rsNormaAlteracao);

            $stJs .= "jq('#inCodLei').val('".$_REQUEST['inCodLei']."');\n";
            $stJs .= "jq('#stFundamentacaoLegal').html('".$rsNormaAlteracao->getCampo('nom_norma')."');";
            $stJs .= "jq('#stDataNorma').html('".$rsNormaAlteracao->getCampo('dt_assinatura')."');";
            }else{
                $stJs .= "jq('#inCodLei').val('');\n";
                $stJs .= "jq('#stFundamentacaoLegal').html('&nbsp;');";
                $stJs .= "jq('#stDataNorma').html('&nbsp;');";
            }
        echo $stJs;
        
    break;

    case "montaListasPublicidadeRescisao":
        // carrega mapeamentos
        require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoPublicacaoRescisaoConvenio.class.php" );
        require_once ( CAM_GA_CGM_NEGOCIO . "RCGM.class.php");
        // cria instancia de cgm
        $obCgm = new RCGM;

        //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
        $obPubRescisaoConvenio = new TLicitacaoPublicacaoRescisaoConvenio();
        $obPubRescisaoConvenio->setDado('exercicio_convenio', $_REQUEST['inExercicio']);
        $obPubRescisaoConvenio->setDado('num_convenio', $_REQUEST['inNumConvenio']);
        $obPubRescisaoConvenio->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );

        $inCount = 0;
        $arValores = array();
        while ( !$rsVeiculosPublicacao->eof() ) {
            $arValores[$inCount]['id'            ]   = $inCount + 1;
            $arValores[$inCount]['inVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
            $arValores[$inCount]['stVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'nom_veiculo' );
            $arValores[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao' );
            $arValores[$inCount]['inNumPublicacao']  = $rsVeiculosPublicacao->getCampo( 'num_publicacao' );
            $arValores[$inCount]['_stObservacao'  ]  = $rsVeiculosPublicacao->getCampo( 'observacao' );
            $inCount++;
            $rsVeiculosPublicacao->proximo();
        }

        Sessao::write('arValores', $arValores);
        $stJs = montaListaVeiculos ( $arValores );

        /*
            O campo vem bloqueado pois o usuário somente poderá alterar a data de
            rescisão de não houver nada na listagem. Assim não tem como o usuário
            trocar a data para uma data anterior de uma data já cadastrada
            na listagem
        */

        if (!empty($arVeiculos)) {
            $stJs = "d.getElementById('dtRescisao').readOnly = true;";
        }
        echo montaListaVeiculos ( $rsVeiculos, $stJs, true );
    break;

    //Inclui itens na listagem de veiculos de publicacao utilizados
    case 'incluirListaVeiculos':
        $stMensagem = '';
        $arValores = Sessao::read('arValores');
        if ($_REQUEST['inVeiculo'] == '') {
            $stMensagem = 'Preencha o campo Veículo de Publicação!';
        }
        if ($_REQUEST['dtDataPublicacao'] == '') {
            $stMensagem = 'Preencha o campo Data de Publicação!';
        }
        $boPublicacaoRepetida = false;
        if ( is_array( $arValores ) ) {
            foreach ($arValores as $arTEMP) {
                if ($arTEMP['inVeiculo'] == $_REQUEST["inVeiculo"] & $arTEMP['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao']) {
                    $boPublicacaoRepetida = true ;
                    $stMensagem = "Este veículos de publicação já está na lista.";
                }
            }
        }
        if (!$boPublicacaoRepetida AND $stMensagem == '') {
            $inCount = sizeof($arValores);
            $arValores[$inCount]['id'             ] 	= $inCount + 1;
            $arValores[$inCount]['inVeiculo'      ] 	= $request->get("inVeiculo");
            $arValores[$inCount]['stVeiculo'      ] 	= $request->get("stNomCgmVeiculoPublicadade");
            $arValores[$inCount]['dtDataPublicacao' ] 	= $request->get("dtDataPublicacao");
            $arValores[$inCount]['inNumPublicacao' ] 	= $request->get("inNumPublicacao");
            $arValores[$inCount]['_stObservacao'   ] 	= $request->get("_stObservacao");
            $arValores[$inCount]['inCodCompraDireta' ] 	= $request->get("HdnCodCompraDireta");
        } else {
            echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }

        Sessao::write('arValores', $arValores);

        echo montaListaVeiculos( $arValores);
        $js ="$('HdnCodVeiculo').value ='';";
        $js.="$('inVeiculo').value ='';";
        $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
        $js.="$('inNumPublicacao').value ='';";
        $js.="$('_stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
        echo $js;
    break;

    //Carrega itens da listagem de veiculos de publicacao utilizados em seus determinados campos no Form.
    case 'alterarListaVeiculos':
        $i = 0;

        $arValores = Sessao::read('arValores');
        if ( is_array($arValores)) {
        foreach ($arValores as $key => $value) {
            if (($key+1) == $_REQUEST['id']) {
            $js ="$('HdnCodVeiculo').value                      ='".$_REQUEST['id']."';                         ";
            $js.="$('inVeiculo').value                          ='".$arValores[$i]['inVeiculo']."';             ";
            $js.="$('dtDataPublicacao').value                   ='".$arValores[$i]['dtDataPublicacao']."';      ";
            $js.="$('inNumPublicacao').value                    ='".$arValores[$i]['inNumPublicacao']."';       ";
            $js.="$('_stObservacao').value                       ='".$arValores[$i]['_stObservacao']."';        ";
            $js.="$('stNomCgmVeiculoPublicadade').innerHTML='".$arValores[$i]['stVeiculo']."';                  ";
            $js.="$('incluiVeiculo').value    ='Alterar';                                                        ";
            $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'alteradoListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta, HdnCodVeiculo\')');";
            }
            $i++;
        }
        }
        echo $js;
    break;

    //Confirma itens alterados da listagem de veiculos de publicacao utilizados
    case "alteradoListaVeiculos":
         $inCount = 0;
         $boDotacaoRepetida = false;
         $arValores = Sessao::read('arValores');
         foreach ($arValores as $key=>$value) {
        if ($value['inVeiculo'] == $_REQUEST["inVeiculo"] & $value['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao'] AND ( $key+1 != $_REQUEST['HdnCodVeiculo'] ) ) {
            $boDotacaoRepetida = true ;
            break;
        }
         }
         if (!$boDotacaoRepetida) {
           foreach ($arValores as $key=>$value) {
            if (($key+1) == $_REQUEST['HdnCodVeiculo']) {
              $arValores[$inCount]['id'            ] = $inCount + 1;
              $arValores[$inCount]['inVeiculo'     ] = $_REQUEST[ "inVeiculo"                  ];
              $arValores[$inCount]['stVeiculo'     ] = sistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$_REQUEST['inVeiculo'].' ');
              $arValores[$inCount]['dtDataPublicacao'] = $_REQUEST[ "dtDataPublicacao"         ];
                      $arValores[$inCount]['inNumPublicacao']  = $_REQUEST[ "inNumPublicacao"          ];
              $arValores[$inCount]['_stObservacao'  ]   = $_REQUEST[ "_stObservacao"             ];
            }
             $inCount++;
           }
           Sessao::write('arValores', $arValores);
           $js =montaListaVeiculos($arValores);
           $js.="$('HdnCodVeiculo').value ='';";
           $js.="$('inVeiculo').value ='';";
           $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
                   $js.="$('inNumPublicacao').value ='';";
           $js.="$('_stObservacao').value = '';";
           $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
           $js.="$('incluiVeiculo').value = 'Incluir';";
           $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
           echo $js;

        } else {
           echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
        }
    break;

    //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaVeiculos':

        $boDotacaoRepetida = false;
        $arTEMP            = array();
        $inCount           = 0;
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $key => $value) {
        if (($key+1) != $_REQUEST['id']) {
            $arTEMP[$inCount]['id'               ] = $inCount + 1;
            $arTEMP[$inCount]['inVeiculo'        ] = $value[ "inVeiculo"      ];
            $arTEMP[$inCount]['stVeiculo'        ] = $value[ "stVeiculo"      ];
            $arTEMP[$inCount]['dtDataPublicacao' ] = $value[ "dtDataPublicacao" ];
                $arTEMP[$inCount]['inNumPublicacao'  ] = $value[ "inNumPublicacao"  ];
            $arTEMP[$inCount]['_stObservacao'     ] = $value[ "_stObservacao"   ];
            $arTEMP[$inCount]['inCodCompraDireta'   ] = $value[ "inCodCompraDireta" ];
            $inCount++;
        }
        }
        Sessao::write('arValores', $arTEMP);
        echo montaListaVeiculos($arTEMP);
     break;

    case 'limparVeiculo' :
        $js ="$('HdnCodVeiculo').value ='';";
        $js.="$('inVeiculo').value ='';";
        $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
            $js.="$('inNumPublicacao').value ='';";
        $js.="$('_stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
        echo $js;
    break;

    //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
    case 'carregaListaVeiculos' :
        $arValores = Sessao::read('arValores');
        echo montaListaVeiculos($arValores);
    break;
    }
