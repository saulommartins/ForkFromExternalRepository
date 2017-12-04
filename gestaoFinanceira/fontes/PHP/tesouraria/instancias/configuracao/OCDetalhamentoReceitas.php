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
    * Paginae Oculta para funcionalidade Manter receitas
    * Data de Criação   : 05/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCDetalhamentoReceitas.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "DetalhamentoReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//include_once( $pgJs );

$stCtrl = $_REQUEST['stCtrl'];

function montaLista($arRecordSet ,$boExecuta = true)
{
    // recordset
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    // table
    $table = new TableTree();
    $table->setRecordset( $rsLista );
    $table->setArquivo('OCDetalhamentoReceitas.php');
    $table->setParametros( array( 'cod_dedutora'=>'cod_dedutora', 'desc_dedutora' => 'desc_dedutora' ) );
    $table->setComplementoParametros( 'stCtrl=montaDetalheDedutora');

    // Defina o título da tabela
    $table->setSummary( $stTituloTable );
    $table->addCondicionalTree( 'dedutora' , 't' );

    // lista zebrada
    //$table->setConditional( true , "#efefef" );
    $table->setSummary( 'Lista de Créditos e Acréscimos' );

    $table->Head->addCabecalho( 'Código Crédito' , 10  );
    $table->Head->addCabecalho( 'Tipo do Crédito' , 10  );
    $table->Head->addCabecalho( 'Descrição' , 60  );

    $table->Body->addCampo( 'codigo', 'C' );
    $table->Body->addCampo( 'descricao_acrescimo', 'C' );
    $table->Body->addCampo( 'desc', 'E' );

    $table->Body->addAcao('excluir','executaFuncaoAjax( \'%s\', \'&credito=%s&acrescimo=%s&tipo=%s&divida=%s\' )', array( 'excluiCredito','codigo', 'cod_acrescimo' , 'cod_tipo', 'divida_ativa' ) );

    $table->montaHTML();

    // retorno
    if ($boExecuta) {
        $stHTML = $table->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        $stJs = "$('spnLista').innerHTML='".$stHTML."';\r\n";
        echo $stJs;

    } else {
        return $table->getHTML();
    }

}

function normalize($n) { return $n * 1 ;}  // isso faz 001 virar 1 para comparação dos creditos

switch ($stCtrl) {
    case 'montaLista':
        $arCredito = Sessao::read('arCredito');
        if ( count($arCredito)>0 ) {
            $stJs .=  montaLista( $arCredito );
        }
        echo $stJs;
    break;

    case 'insereCredito':
        $obErro = new Erro();

        if ($_REQUEST['inCodCredito'] == '') {
            $obErro->setDescricao('Informe o crédito');
        } elseif ($_REQUEST['inCodAcrescimo'] == '') {
            $obErro->setDescricao('Selecione o tipo do crédito');
        } elseif ($_GET['inCodAcrescimo'] == '0' AND $_GET['stTipoReceita'] != 'extra' AND $_GET['inCodDedutora'] == '') {
            $obErro->setDescricao('Selecione a receita dedutora para o crédito principal');
        }

        if ( !$obErro->ocorreu() ) {
            // desmembrar credito

            $boDividaAtiva = ($_GET['boDividaAtiva'] == 'S') ? '1' : '0';

            $arCreditoTmp = explode( '.' , $_REQUEST['inCodCredito'] );
            $arCreditoTmp = array_map( 'normalize', $arCreditoTmp );
            $stCreditoTmp = $arCreditoTmp[0].$arCreditoTmp[1].$arCreditoTmp[2].$arCreditoTmp[3].$boDividaAtiva;

            // desmenbrar acrescimo, se existir
            if ($_REQUEST['inCodAcrescimo'] != 0) {
                list( $inCodAcrescimo , $inCodTipo) = explode('.',$_REQUEST['inCodAcrescimo']);
            }
            if ($inCodTipo == '') {
                $inCodTipo = 0;
            }

            $arTmpCredito = Sessao::read('arCredito');

            foreach ($arTmpCredito as $arCredito) {

                $Credito = $arCredito['codigo'];
                $Credito = explode( '.' , $Credito );
                $arCreditoTmp2 = array_map( 'normalize', $Credito );
                $stCreditoTmp2 = $arCreditoTmp2[0].$arCreditoTmp2[1].$arCreditoTmp2[2].$arCreditoTmp2[3].$arCredito['divida_ativa'];

                if ($stCreditoTmp == $stCreditoTmp2 and $arCredito['cod_acrescimo'] == $inCodAcrescimo and  $arCredito['cod_tipo'] == $inCodTipo) {
                    $obErro->setDescricao('O Crédito/Acréscimos escolhido já está na lista');
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            // buscar descrição do credito
            $stWhere  = " where cod_credito=". trim ( $arCreditoTmp[0] );
            $stWhere .= " and cod_especie=". trim ( $arCreditoTmp[1] ) ;
            $stWhere .= " and cod_genero=". trim ( $arCreditoTmp[2] ) ;
            $stWhere .= " and cod_natureza=". trim ( $arCreditoTmp[3] ) ;

            $stDescricao = SistemaLegado::pegaDado( 'descricao_credito' , 'monetario.credito' , $stWhere );

            $stDescricaoAcrescimo = null;
            // buscar descricao do acrescimo se existir
            if ($inCodAcrescimo != 0) {
                $stWhere  = " where cod_acrescimo=". trim ( $inCodAcrescimo );
                $stWhere .= " and cod_tipo=". trim ( $inCodTipo ) ;
                $stDescricaoAcrescimo = SistemaLegado::pegaDado( 'descricao_acrescimo' , 'monetario.acrescimo' , $stWhere );
            } else {
                $stDescricaoAcrescimo = 'Principal';
            }
            if ($boDividaAtiva == 1) {
                $stDescricaoAcrescimo .= '/Dívida Ativa';
            }

            // cria array pro novo credito/acrescimo
            $arNewCredito = array( 'cod_credito'      => $arCreditoTmp[0]
                                 , 'cod_especie'         => $arCreditoTmp[1]
                                 , 'cod_genero'          => $arCreditoTmp[2]
                                 , 'cod_natureza'        => $arCreditoTmp[3]
                                 , 'divida_ativa'        => $boDividaAtiva
                                 , 'codigo'              => $arCreditoTmp[0].'.'.$arCreditoTmp[1].'.'.$arCreditoTmp[2].'.'.$arCreditoTmp[3]
                                 , 'desc'                => $stDescricao
                                 , 'cod_acrescimo'       => $inCodAcrescimo
                                 , 'cod_tipo'            => $inCodTipo
                                 , 'descricao_acrescimo' => $stDescricaoAcrescimo
            );

            if ($_GET['inCodDedutora'] != '') {
                include_once( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReceita.class.php' );
                $obTOrcamentoReceita = new TOrcamentoReceita();
                $obTOrcamentoReceita->recuperaRelacionamentoContaReceita( $rsDedutora, " AND ORE.cod_receita = ".$_GET['inCodDedutora']." AND ORE.exercicio = '".Sessao::getExercicio()."' " );

                $arNewCredito['dedutora'] = 't';
                $arNewCredito['cod_dedutora'] = $_GET['inCodDedutora'];
                $arNewCredito['desc_dedutora'] = $rsDedutora->getCampo('descricao');
            }

            // adiciona ao array de creditos/acrescimos
            $arTmpCredito = Sessao::read('arCredito');
            $arTmpCredito[] = $arNewCredito;
            Sessao::write('arCredito', $arTmpCredito);

            $stJs = montaLista( Sessao::read('arCredito'), 'orcamentaria', true );
            $stJs .= "limpaCredito();";
        } else {
            $stJs  = "alertaAviso( '".$obErro->getDescricao()."!()','form','erro','".Sessao::getId()."' );";
        }

        echo $stJs;

    break;

    case 'excluiCredito':

        // inicializacao
        $arCredito = Sessao::read('arCredito');
        $arNewCredito = array();
        $inCount = 0;
        $_REQUEST['acrescimo'] = $_REQUEST['acrescimo'] == 'cod_acrescimo' ? null : $_REQUEST['acrescimo'] ;
        //$_REQUEST['tipo'] = $_REQUEST['tipo'] == 'cod_tipo' ? 0 : $_REQUEST['tipo'] ;

        // recupera string credito ( 1.2.3.4 ) explode em um array e normaliza caso esteja com pad ( 001.002.01.1)
        $arCreditoExcluir  = array_map( 'normalize', explode('.',$_REQUEST['credito']) );

        // varre lista procurando pelo credito/acrescimo a excluir
        foreach ($arCredito as $Credito) {
            $arCreditoCorrente  = array_map( 'normalize', explode('.',$Credito['codigo'])	);

            //monta chaves
            $stChaveCorrente = $arCreditoCorrente[0].'.'.$arCreditoCorrente[1].'.'.$arCreditoCorrente[2].'.'.$arCreditoCorrente[3].'.'.$_REQUEST['acrescimo'].'.'.$_REQUEST['tipo'].'.'.$Credito['divida_ativa'];
            $stChaveExcluir = $arCreditoExcluir[0].'.'.$arCreditoExcluir[1].'.'.$arCreditoExcluir[2].'.'.$arCreditoExcluir[3].'.'.$Credito['cod_acrescimo'].'.'.$Credito['cod_tipo'].'.'.$_GET['divida'];
            // caso tenha diferença entre o array a excluir e o corrente ele continua
            if ($stChaveCorrente != $stChaveExcluir) {
                $arNewCredito[$inCount]['cod_credito'      	] = $Credito['cod_credito'        ];
                $arNewCredito[$inCount]['cod_especie'      	] = $Credito['cod_especie'        ];
                $arNewCredito[$inCount]['cod_genero'      	] = $Credito['cod_genero'  	   ];
                $arNewCredito[$inCount]['cod_natureza' 		] = $Credito['cod_natureza'       ];
                $arNewCredito[$inCount]['divida_ativa' 		] = $Credito['divida_ativa'       ];
                $arNewCredito[$inCount]['codigo'      		] = $Credito['codigo'        	   ];
                $arNewCredito[$inCount]['desc'    			] = $Credito['desc'      	       ];
                $arNewCredito[$inCount]['cod_acrescimo'		] = $Credito['cod_acrescimo'	   ];
                $arNewCredito[$inCount]['cod_tipo'     		] = $Credito['cod_tipo'		   ];
                $arNewCredito[$inCount]['descricao_acrescimo'] = $Credito['descricao_acrescimo'];
                if ($Credito['cod_dedutora'] != '') {
                    $arNewCredito[$inCount]['dedutora'     ] = 't';
                    $arNewCredito[$inCount]['cod_dedutora' ] = $Credito['cod_dedutora' ];
                    $arNewCredito[$inCount]['desc_dedutora'] = $Credito['desc_dedutora'];
                }
                $inCount++;
            }
        }

        Sessao::write('arCredito', $arNewCredito);

         if (count( $arNewCredito ) < 1) {
            $stJs .= "parent.frames['telaPrincipal'].document.frm.inCodCredito.value=''; ";
            $stJs .= "parent.frames['telaPrincipal'].document.getElementById('stCredito').innerHTML='&nbsp;';";
            $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnAcrescimo').innerHTML='';";
            $stJs .= "parent.frames['telaPrincipal'].document.frm.inCodEntidade.value='';";

            if ($_REQUEST['stTipoReceita'] ==  'extra') {
                $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnEntidade').innerHTML='';";
            }
            echo $stJs;
         }

        $stJs .= montaLista( Sessao::read('arCredito') , true);
        echo $stJs;
    break;
    case "montaEntidade":

         include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );
         $obREntidade = new ROrcamentoEntidade;
         $obREntidade->setExercicio( $_REQUEST['stExercicio'])  ;
         $obREntidade->setCodigoEntidade( $_REQUEST['stCodEntidade']) ;
         $obREntidade->consultarnomes( $rsLista );
         $obErro = $obREntidade->consultarNomes( $rsLista );

         $obFormulario = new Formulario();

         $obREntidade->setExercicio( Sessao::getExercicio() )  ;
         $obREntidade->setCodigoEntidade( $_REQUEST['stCodEntidade'] ) ;
         $obREntidade->consultarnomes( $rsLista );
         $obErro = $obREntidade->consultarNomes( $rsLista );

         // Define objeto Label para codigo da entidade da conta
         $obLblCodEntidade = new Label();
         $obLblCodEntidade->setRotulo( 'Código Entidade'            );
         $obLblCodEntidade->setName( 'lblCodEntidade'            );
         $obLblCodEntidade->setId( 'lblCodEntidade'            );
         $obLblCodEntidade->setValue ( $_REQUEST['stCodEntidade']." - ".$rsLista->getCampo('entidade') );

         $obFormulario->addComponente      ( $obLblCodEntidade );

         $obFormulario->montaInnerHTML();
         $stHTML = $obFormulario->getHTML();

         if ($stHTML)
         $stJs = "d.getElementById('spnEntidade').innerHTML = '".$stHTML."';\n";
         echo $stJs;

    break;

    case 'montaComboAcrescimos':

        if ($_REQUEST['inCodEntidade']) {
         include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );
         $obREntidade = new ROrcamentoEntidade;

         $obFormulario2 = new Formulario();

         $obREntidade->setExercicio( Sessao::getExercicio() )  ;
         $obREntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] ) ;
         $obREntidade->consultarnomes( $rsLista );
         $obErro = $obREntidade->consultarNomes( $rsLista );

         // Define objeto Label para codigo da entidade da conta
         $obLblCodEntidade = new Label();
         $obLblCodEntidade->setRotulo( 'Código Entidade'            );
         $obLblCodEntidade->setValue ( $_REQUEST['inCodEntidade']." - ".$rsLista->getCampo('entidade') );

         $obFormulario2->addComponente      ( $obLblCodEntidade       );

         $obFormulario2->montaInnerHTML();
         $stHTML2 = $obFormulario2->getHTML();
         echo "d.getElementById('spnEntidade').innerHTML = '".$stHTML2."';";
        }

        if ( !$_REQUEST['inCodCredito'] ) die;

        include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";
        $obRMONCredito = new RMONCredito;
        $obRMONCredito->consultarMascaraCredito();
        $stMascaraCredito = $obRMONCredito->getMascaraCredito();

        if (strlen($_REQUEST['inCodCredito']) < strlen($stMascaraCredito)) {
            $stJs .= "alertaAviso( 'Crédito informado não existe.','form','erro','".Sessao::getId()."' );";
            $stJs .= "limpaCredito();";
            echo $stJs; die;
        }

        // pegar credito
        list($Credito,$Especie,$Genero,$Natureza) = explode('.',$_REQUEST['inCodCredito'] );

        $stWhere  = " AND ca.cod_tipo = ac.cod_tipo ";
        $stWhere .= "where ca.cod_credito=". trim ( $Credito );
        $stWhere .= " and ca.cod_especie=". trim ( $Especie ) ;
        $stWhere .= " and ca.cod_genero=". trim ( $Genero ) ;
        $stWhere .= " and ca.cod_natureza=". trim ( $Natureza ) ;
        /*$stWhere .= " AND NOT EXISTS ( SELECT 1
                                         FROM orcamento.receita_credito_acrescimo
                                        WHERE receita_credito_acrescimo.cod_tipo = ca.cod_tipo
                                          AND receita_credito_acrescimo.cod_acrescimo = ca.cod_acrescimo
                                          AND receita_credito_acrescimo.cod_credito = ca.cod_credito
                                          AND receita_credito_acrescimo.cod_natureza = ca.cod_natureza
                                          AND receita_credito_acrescimo.cod_genero = ca.cod_genero
                                          AND receita_credito_acrescimo.cod_especie = ca.cod_especie
                                      ) ";*/
        // montar combo com acrescimos do credito
        require_once ( CAM_GT_MON_MAPEAMENTO . "TMONCreditoAcrescimo.class.php");
        $obTMONCredAcres =  new TMONCreditoAcrescimo();
        $obErro = $obTMONCredAcres->executaRecupera("montaRecuperaAcrescimosDoCredito" , $rsAcrescimos , $stWhere , $stOrder , $boTransacao);

        if (!$obErro->ocorreu() ) {
            $obCmbAcrescimo = new Select();
            $obCmbAcrescimo->setRotulo ( "Tipo do Crédito"                 );
            $obCmbAcrescimo->setName   ( "inCodAcrescimo"                  );
            $obCmbAcrescimo->setId 	   ( "inCodAcrescimo"                  );
            $obCmbAcrescimo->setTitle  ( "Selecione o tipo do crédito que deseja vincular a uma receita." );
            $obCmbAcrescimo->addOption ( ""            ,"Selecione"          );
            $obCmbAcrescimo->addOption ( "0"           ,"Principal"          );
            $obCmbAcrescimo->setValue  ( $stTipoReceita                      );
            $obCmbAcrescimo->setCampoId            ( "[cod_acrescimo].[cod_tipo]"  );
            $obCmbAcrescimo->setCampoDesc          ( "descricao_acrescimo"  );
            $obCmbAcrescimo->preencheCombo         ( $rsAcrescimos     );

            $obCmbAcrescimo->setNull   ( true                                );
            $obCmbAcrescimo->setObrigatorioBarra( true );
            $obCmbAcrescimo->obEvento->setOnChange("montaParametrosGET('montaDedutora','inCodAcrescimo,stTipoReceita');");

            if ($rsAcrescimos->getNumLinhas() == -1 ) {
                 include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php");

                 $obTMONCredito = new TMONCredito;
                 $obTMONCredito->recuperaMascaraCredito( $rsMascara );
                 if ( !$rsMascara->eof() ) {
                    $stMascaraCredito = $rsMascara->getCampo("mascara_credito");
                 }

                 $inCodCredito  = $_REQUEST['inCodCredito'];

                 if ($inCodCredito) {
                    if (strlen($inCodCredito) >= strlen($stMascaraCredito) ) {
                       $inCodCreditoComposto = explode('.', $inCodCredito );
                       $stFiltro = "WHERE ";
                       $stFiltro .= " \n mc.cod_credito = ".$inCodCreditoComposto[0]." AND ";
                       $stFiltro .= " \n me.cod_especie = ".$inCodCreditoComposto[1]." AND ";
                       $stFiltro .= " \n mg.cod_genero = ".$inCodCreditoComposto[2]." AND ";
                       $stFiltro .= " \n mn.cod_natureza = ".$inCodCreditoComposto[3];
                       $obTMONCredito->recuperaRelacionamento( $rsGrupos, $stFiltro );
                       if ( $rsGrupos->eof() ) {
                           echo "limpaCredito();";
                           die;
                       }

                    }
                 }
            }

            if ($_GET['stTipoReceita'] == 'orcamentaria') {
                //Define um objeto do tipo check para a divida ativa
                $obSimNao = new SimNao();
                $obSimNao->setName   ('boDividaAtiva');
                $obSimNao->setId     ('boDividaAtiva');
                $obSimNao->setRotulo ('Dívida Ativa');
                $obSimNao->setTitle  ('Informe se o crédito é da dívida ativa');
                $obSimNao->setChecked('N');
            }

            $obSpnDedutora = new Span();
            $obSpnDedutora->setId('spnDedutora');

            $obFormulario = new Formulario;
            $obFormulario->addComponente ($obCmbAcrescimo  );
            if ($_GET['stTipoReceita'] == 'orcamentaria') {
                $obFormulario->addComponente ($obSimNao        );
            }
            $obFormulario->addSpan 		 ($obSpnDedutora  );
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            echo "d.getElementById('spnAcrescimo').innerHTML = '".$stHTML."';";

        } else {

            $stJs .= "alertaAviso( 'Crédito informado não existe ou não esta vinculado com uma Conta-Corrente.','form','erro','".Sessao::getId()."' );";
            $stJs .= "limpaCredito();";
            echo $stJs;
            die;
        }

    break;

    case 'montaDedutora' :
        /**
         * Monta o formulario para incluir a conta dedutora ( conta onde vão os descontos )
         */
        $obFormulario = new Formulario ();

        if ($_GET['inCodAcrescimo'] == '0' AND $_GET['stTipoReceita'] != 'extra') {
            include_once( CAM_GF_ORC_COMPONENTES."IPopUpReceita.class.php" );

            //Define objeto BuscaInner para a dedutora
            $obIPopUpReceita = new IPopUpReceita   ();
            $obIPopUpReceita->obCampoCod->setName	( 'inCodDedutora' );
            $obIPopUpReceita->obCampoCod->setId	( 'inCodDedutora' );
            $obIPopUpReceita->setRotulo				( 'Dedutora' );
            $obIPopUpReceita->setTitle				( 'Informe a receita dedutora do crédito principal' );
            $obIPopUpReceita->setTipoBusca			( 'receitaDedutoraExportacao' );
            $obIPopUpReceita->setNull				( true );
            $obIPopUpReceita->setObrigatorioBarra	( true );

            $obFormulario->addComponente ($obIPopUpReceita);

        }
        $obFormulario->montaInnerHTML ();
        echo "jq('#spnDedutora').html('".$obFormulario->getHTML()."');";

        break;

    case 'montaDetalheDedutora' :
        /**
         * Monta o detalhe do credito principal, para demonstrar a dedutora
         */

        $rsLista = new RecordSet;
        $rsLista->preenche( array(array('cod_dedutora'=>$_REQUEST['cod_dedutora'],'desc_dedutora'=>$_REQUEST['desc_dedutora'])) );
        // table
        $table = new Table();
        $table->setRecordset( $rsLista );

        // lista zebrada
        //$table->setConditional( true , "#efefef" );
        $table->setSummary( 'Dedutora' );

        $table->Head->addCabecalho( 'Código Reduzido' , 10  );
        $table->Head->addCabecalho( 'Descrição' , 70  );

        $table->Body->addCampo( 'cod_dedutora', 'C' );
        $table->Body->addCampo( 'desc_dedutora', 'E' );

    //    $table->Body->addAcao('excluir','executaFuncaoAjax( \'%s\', \'&credito=%s&acrescimo=%s&tipo=%s\' )', array( 'excluiCredito','codigo', 'cod_acrescimo' , 'cod_tipo' ) );

        $table->montaHTML();

        echo $table->getHTML();

        break;

    case 'limpaCredito' :

       $stJs .= "parent.frames['telaPrincipal'].document.frm.inCodCredito.value=''; ";
       $stJs .= "parent.frames['telaPrincipal'].document.getElementById('stCredito').innerHTML='&nbsp;';";
       $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnAcrescimo').innerHTML='';";

       if ($_REQUEST['stTipoReceita'] ==  'extra')
       $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnEntidade').innerHTML='';";
       echo $stJs;
    break;
}
