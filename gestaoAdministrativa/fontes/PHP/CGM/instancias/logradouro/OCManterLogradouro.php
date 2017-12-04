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
    * Página de processamento oculto para o cadastro de logradouro
    * Data de Criação   : 08/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: OCProcurarLogradouro.php 62960 2015-07-13 14:00:58Z evandro $

    * Casos de uso: uc-05.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO.'RCIMLogradouro.class.php' );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoLogradouro.class.php"       );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoLogradouro.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php"             );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrecho.class.php" );

// Guarda a ação antiga para ser escrita ao final do script.
$acao   = Sessao::read('acao');
$modulo = Sessao::read('modulo');

//Define o nome dos arquivos PHP
$stPrograma          = "ManterLogradouro";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgFormVerificaNivel = "FMProcurarLogradouroVerificaNivel.php";
$pgFormNivel         = "FMProcurarLogradouroNivel.php";
$pgFormUltimoNivel   = "FMProcurarLogradouroUltimoNivel.php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";
$pgBairro            = CAM_GT_CIM_POPUPS."/bairro/FMManterBairro.php?".Sessao::getId();;

include_once( $pgJs );

// INSTANCIA OBJETO
$obRCIMBairro = new RCIMBairro;

// FUNCOES PARA MONTAR LISTAS
function montaListaBairro($arListaBairros, $boRetorna = false, $boExcluir = true)
{
    if ( count( $arListaBairros ) ) {

        $rsListarBairros = new RecordSet;
        $rsListarBairros->preenche ( $arListaBairros   );

        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListarBairros   );
        $obLista->setTitulo                    ( "Lista de Bairros" );
        $obLista->setMostraPaginacao           ( false              );
        $obLista->addCabecalho                 (                    );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"           );
        $obLista->ultimoCabecalho->setWidth    ( 2                  );
        $obLista->commitCabecalho              (                    );
        $obLista->addCabecalho                 (                    );
        $obLista->ultimoCabecalho->addConteudo ( "Código do Bairro" );
        $obLista->ultimoCabecalho->setWidth    ( 10                 );
        $obLista->commitCabecalho              (                    );
        $obLista->addCabecalho                 (                    );
        $obLista->ultimoCabecalho->addConteudo ( "Nome do Bairro"   );
        $obLista->ultimoCabecalho->setWidth    ( 40                 );
        $obLista->commitCabecalho              (                    );
        if ($boExcluir) {
            $obLista->addCabecalho                 (                    );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"           );
            $obLista->ultimoCabecalho->setWidth    ( 2                  );
            $obLista->commitCabecalho              (                    );
        }

        $obLista->addDado                      (                    );
        $obLista->ultimoDado->setCampo         ( "cod_bairro"       );
        $obLista->ultimoDado->setAlinhamento   ( "DIREITA"          );
        $obLista->commitDado                   (                    );
        $obLista->addDado                      (                    );
        $obLista->ultimoDado->setCampo         ( "nom_bairro"       );
        $obLista->commitDado                   (                    );

        if ($boExcluir) {
            $obLista->addAcao                      (                    );
            $obLista->ultimaAcao->setAcao          ( "EXCLUIR"          );
            $obLista->ultimaAcao->setFuncao        ( true               );
            $obLista->ultimaAcao->setLink          ( "JavaScript:excluirBairro();" );
            $obLista->ultimaAcao->addCampo         ("1","cod_bairro"    );
            $obLista->commitAcao                   (                    );    
        }

        $obLista->montaHTML                    (                    );
        $stHTML =  $obLista->getHtml           (                    );
        $stHTML = str_replace                  ("\n","",$stHTML     );
        $stHTML = str_replace                  ("  ","",$stHTML     );
        $stHTML = str_replace                  ("'","\\'",$stHTML   );

    } else {

        $stHTML = "&nbsp";

    }

    $js .= "d.getElementById('spanListarBairro').innerHTML = '".$stHTML."';\n";
    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaFrameOculto($js);
    }
}

function montaListaCEP($arListaCEP, $boRetorna = false, $boExcluir = true)
{
    if ( count( $arListaCEP ) ) {

        $rsListarCEP = new RecordSet;
        $rsListarCEP->preenche ( $arListaCEP     );

        function corrige_cep($valor)
        {
            if ( strlen($valor["cep"]) == 8 && is_int(strlen($valor["cep"]))) {
                $valor["cep"] = substr($valor["cep"],0,5).'-'.substr($valor["cep"],5,3);
            }
            if ( strlen($valor["num_inicial"])<1 )
                $valor["num_inicial"] = " &nbsp; ";
            if ( strlen($valor["num_final"])<1 )
                $valor["num_final"] = " &nbsp; ";
            if ( strlen($valor["numeracao"])<1 )
                $valor["numeracao"] = " &nbsp; ";

            return $valor;

        }
        $rsListarCEP->arElementos = array_map("corrige_cep",$rsListarCEP->arElementos);

        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListarCEP     );
        $obLista->setTitulo                    ( "Lista de CEP's" );
        $obLista->setMostraPaginacao           ( false            );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
        $obLista->ultimoCabecalho->setWidth    ( 2                );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "CEP"            );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "N&uacute;mero Inicial" );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "N&uacute;mero Final"   );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        $obLista->addCabecalho                 (                  );
        $obLista->ultimoCabecalho->addConteudo ( "Numera&ccedil;&atilde;o"      );
        $obLista->ultimoCabecalho->setWidth    ( 15               );
        $obLista->commitCabecalho              (                  );
        if ($boExcluir) {
            $obLista->addCabecalho                 (                  );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
            $obLista->ultimoCabecalho->setWidth    ( 2                );
            $obLista->commitCabecalho              (                  );
        }

        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "cep"            );
        $obLista->commitDado                   (                  );
        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "num_inicial"    );
        $obLista->commitDado                   (                  );
        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "num_final"      );
        $obLista->commitDado                   (                  );
        $obLista->addDado                      (                  );
        $obLista->ultimoDado->setCampo         ( "numeracao"      );
        $obLista->commitDado                   (                  );

        if ($boExcluir) {
            $obLista->addAcao                      (                  );
            $obLista->ultimaAcao->setAcao          ( "EXCLUIR"        );
            $obLista->ultimaAcao->setFuncao        ( true             );
            $obLista->ultimaAcao->setLink          ( "JavaScript:excluirCEP();" );
            $obLista->ultimaAcao->addCampo         ( "1","cep"        );
            $obLista->commitAcao                   (                  );
        }

        $obLista->montaHTML                    (                  );
        $stHTML = $obLista->getHtml            (                  );
        $stHTML = str_replace                  ( "\n","",$stHTML  );
        $stHTML = str_replace                  ( "  ","",$stHTML  );
        $stHTML = str_replace                  ( "'","\\'",$stHTML);
    } else {
        $stHTML = "&nbsp";
    }

    $js .= "d.getElementById('spanListarCEP').innerHTML = '".$stHTML."';\n";
    if ($boExcluir) {
        $js .= "f.inCEP.value=''; \n";
        $js .= "f.inInicial.value=''; \n";
        $js .= "f.inFinal.value=''; \n";
        $js .= "f.boNumeracao[0].checked = true; \n";
    }
    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaFrameOculto($js);
    }
}

function montaListaHistorico($arDadosHistorico)
{
    if ($_REQUEST['stAcao'] != 'consultar') {
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
        $obTxtNomeAnterior = new TextBox;
        $obTxtNomeAnterior->setRotulo    ( "*Nome Anterior"              );
        $obTxtNomeAnterior->setTitle     ( "Nome na qual o logradouro era chamado anteriormente" );    
        $obTxtNomeAnterior->setName      ( "stNomeLogradouroAnterior"    );
        $obTxtNomeAnterior->setId        ( "stNomeLogradouroAnterior"    );
        $obTxtNomeAnterior->setSize      ( 70                            );
        $obTxtNomeAnterior->setMaxLength ( 60                            );
        $obTxtNomeAnterior->setNull      ( false                         );
        $obTxtNomeAnterior->setValue     ( str_replace('\\', '', $_REQUEST["stNomeLogradouroAnterior"]));
    
        $obIPopUpNorma = new IPopUpNorma();
        $obIPopUpNorma->obInnerNorma->setRotulo          ( "**Norma"     );
        $obIPopUpNorma->obInnerNorma->setTitle           ( "Informe a Norma que determinou o Nome do Logradouro."    );
        $obIPopUpNorma->obInnerNorma->setId              ( "stNormaHistorico" );
        $obIPopUpNorma->obInnerNorma->obCampoCod->setId  ( "inCodNormaHistorico"  );
        $obIPopUpNorma->obInnerNorma->obCampoCod->setName( "inCodNormaHistorico"  );
        $obIPopUpNorma->obInnerNorma->setNull            ( true );
        
        $obDtInicial = new Data();
        $obDtInicial->setRotulo    ( "**Data Inicial" );
        $obDtInicial->setTitle     ( "Informe a Data Inicial do Nome do Logradouro." );
        $obDtInicial->setName      ( "stDataInicialHistorico" );
        $obDtInicial->setId        ( "stDataInicialHistorico" );
        $obDtInicial->setMaxLength ( 10 );
        $obDtInicial->setSize      ( 10 );
    
        $obDtFinal = new Data();
        $obDtFinal->setRotulo    ( "**Data Final" );
        $obDtFinal->setTitle     ( "Informe a Data Final do Nome do Logradouro." );
        $obDtFinal->setName      ( "stDataFinalHistorico" );
        $obDtFinal->setId        ( "stDataFinalHistorico" );
        $obDtFinal->setMaxLength ( 10 );
        $obDtFinal->setSize      ( 10 );
    
        //Botoes da lista
        $obOkLista  = new Ok(false);
        $obOkLista->setRotulo('Incluir');
        $obOkLista->setValue ('Incluir');
        $obOkLista->setId    ('btIncluir');
        $obOkLista->setName  ('btIncluir');
        $obOkLista->obEvento->setOnClick(" if ( validaCamposLista() ){ manterHistorico('incluirHistoricoLista'); }");
    
        $obLimparLista  = new Button();
        $obLimparLista->setId    ('btLimpaLista');
        $obLimparLista->setName  ('btLimpaLista');
        $obLimparLista->setValue ('Limpar');
        $obLimparLista->obEvento->setOnClick(" manterHistorico('limparHistoricoLista'); ");
    
        $obFormulario = new Formulario();
        $obFormulario->addTitulo             ( "Histórico de Nome do Logradouro"   );
        $obFormulario->addComponente         ( $obTxtNomeAnterior                  );
        $obIPopUpNorma->geraFormulario       ( $obFormulario                       );
        $obFormulario->addComponente         ( $obDtInicial                        );
        $obFormulario->addComponente         ( $obDtFinal                          );
        $obFormulario->defineBarra           ( array( $obOkLista, $obLimparLista ), 'center' );
    
        $obFormulario->montaInnerHTML();
        $stHTMLHistorico = $obFormulario->getHTML();
    
        $js .= "jq_(\"#spanListarHistorico\").html('".$stHTMLHistorico."'); \n";
    }

    $rsLista = new RecordSet();
    $rsLista->preenche($arDadosHistorico);
    
    $obLista = new Lista;
    $obLista->setRecordSet                 ( $rsLista     );
    $obLista->setTitulo                    ( "Histórico do Logradouro" );
    $obLista->setMostraPaginacao           ( false            );
        
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome do Logradouro" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Inicial" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Final" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Exercício" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Norma" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addDado();        
    $obLista->ultimoDado->setCampo( "nome_anterior" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_inicio" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_fim" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "exercicio" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "descricao_norma" );
    $obLista->commitDado();

    if ($_REQUEST['stAcao'] != 'consultar') {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
    
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( " JavaScript:modificaDado('alterarHistoricoLista'); " );
        $obLista->ultimaAcao->addCampo("1" , "inId");
        $obLista->commitAcao();
    
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( " JavaScript:modificaDado('excluirHistoricoLista'); " );
        $obLista->ultimaAcao->addCampo("1" , "inId");
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHTMLLista = $obLista->getHtml();
    $stHTMLLista = str_replace( "\n","",$stHTMLLista );
    $stHTMLLista = str_replace( "  ","",$stHTMLLista );
    $stHTMLLista = str_replace( "'","\\'",$stHTMLLista );    
        
    $js .= "jq_('#spanListarHistorico').append('".$stHTMLLista."'); \n";
    
    return $js;
    
}

function carregaDados()
{
    GLOBAL $request;
    
    $stAcao = $request->get('stAcao');
    // DEFINE OBJETOS DAS CLASSES
    $obRCIMConfiguracao = new RCIMConfiguracao;
    $obRCIMLogradouro   = new RCIMLogradouro;
    $obRCIMBairro       = new RCIMBairro;
    $rsTipos            = new RecordSet;
    
    //Busca UF e Municipio que foi da configuracao
    $obRCIMConfiguracao->consultarConfiguracao();
    $obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );
    
    //Busca todos os UFs
    $obRCIMLogradouro->listarUF( $rsUF );
    //Busca os tipos de logradouros
    $obRCIMLogradouro->listarTipoLogradouro( $rsTipos );
    //Busca os municipios de acordo com o estado
    $obRCIMLogradouro->setCodigoUF( $arConfiguracao['cod_uf']);
    $obRCIMLogradouro->listarMunicipios( $rsMunicipios );
    //Busca os Bairros de acordo com o estado e municipio
    $obRCIMBairro->setCodigoUF( $arConfiguracao['cod_uf'] );
    $obRCIMBairro->setCodigoMunicipio( $arConfiguracao['cod_municipio'] );
    $obRCIMBairro->listarBairros( $rsBairros );

    //Busca proximo codigo logradouro
    $inProxCodLogradouro = null;
    $obTLogradouro= new TLogradouro();
    $obTLogradouro->proximoCod($inProxCodLogradouro);

    //Preenche Tipo
    if ( $rsTipos->getNumLinhas() > 0 ) {
        foreach ($rsTipos->getElementos() as $key => $value) {
            $stJs .= " jq_('#inCodTipo').append(new Option('".$value['nom_tipo']."','".$value['cod_tipo']."') ); ";
        }
    }    

    //Preenche UF
    if ( $rsUF->getNumLinhas() > 0 ) {
        foreach ($rsUF->getElementos() as $key => $value) {
            $value['nom_uf'] = addslashes($value['nom_uf']);
            $stJs .= " jq_('#inCodUF').append(new Option('".$value['nom_uf']."','".$value['cod_uf']."') ); ";                    
        }
    }
    
    //Preenche Municipios            
    if ( $rsMunicipios->getNumLinhas() > 0 ) {
        foreach ($rsMunicipios->getElementos() as $key => $value) {
            $value['nom_municipio'] = addslashes($value['nom_municipio']);
            $stJs .= " jq_('#inCodMunicipio').append(new Option('".$value['nom_municipio']."','".$value['cod_municipio']."') ); ";                    
        }
    }

    //Preenche Bairros
    if ( $rsBairros->getNumLinhas() > 0 ) {
        foreach ($rsBairros->getElementos() as $key => $value) {
            $value['nom_bairro'] = addslashes($value['nom_bairro']);
            $stJs .= " jq_('#inCodBairro').append(new Option('".$value['nom_bairro']."','".$value['cod_bairro']."') );";
        }
    }

    //De acordo com a acao preenche os campos
    switch ($stAcao) {
        case 'incluir':
            $stJs .= " jq_('#inCodLogradouro').val(".$inProxCodLogradouro."); ";
            $stJs .= " jq_('#inCodigoUF').val('".$arConfiguracao['cod_uf']."'); ";
            $stJs .= " jq_('#inCodUF').val('".$arConfiguracao['cod_uf']."'); ";
            $stJs .= " jq_('#inCodigoMunicipio').val('".$arConfiguracao['cod_municipio']."'); ";
            $stJs .= " jq_('#inCodMunicipio').val('".$arConfiguracao['cod_municipio']."'); ";  
        break;
        
        case 'alterar':
            $obRCIMLogradouro = new RCIMLogradouro;    
            $obRCIMLogradouro->setCodigoUF( $request->get("inCodUF") );
            $obRCIMLogradouro->setCodigoMunicipio( $request->get("inCodMunicipio") );
            $obRCIMLogradouro->setCodigoLogradouro( $request->get("inCodigoLogradouro") );
            $obRCIMLogradouro->listarHistoricoLogradouros( $rsLista, $boTransacao, "" );    
            

            $stJs .= " jq_('#inCodigoLogradouro').html(".$request->get('inCodigoLogradouro')."); ";
            $stJs .= " jq_('#stNomeUF').html('".$request->get('stNomeUF')."'); ";
            $stJs .= " jq_('#stNomeMunicipio').html('".$request->get('stNomeMunicipio')."'); ";
            $stJs .= " jq_('#inCodTipo').val(".$request->get('inCodigoTipo')."); ";
            //buscando o ultimo dado cadastrado de acordo com a data inicial e final
            $rsLista->setUltimoElemento();
            $stJs .= " jq_('#inCodNorma').val(".$rsLista->getCampo('cod_norma')."); ";
            $stJs .= " jq_('#stNorma').html('".$rsLista->getCampo('descricao_norma')."'); ";
            $stJs .= " jq_('#stDataInicial').val('".$rsLista->getCampo('dt_inicio')."'); ";
            $stJs .= " jq_('#stDataFinal').val('".$rsLista->getCampo('dt_fim')."'); ";
            
            foreach ($rsLista->getElementos() as $key => $value) {
                $arDadosHistorico[$key]['inId']             = $key;
                $arDadosHistorico[$key]['sequencial']       = $value['sequencial'];
                $arDadosHistorico[$key]['descricao_norma']  = $value['descricao_norma'];
                $arDadosHistorico[$key]['nome_anterior']    = $value['nome_anterior'];
                $arDadosHistorico[$key]['dt_inicio']        = $value['dt_inicio'];
                $arDadosHistorico[$key]['dt_fim']           = $value['dt_fim'];
                $arDadosHistorico[$key]['exercicio']        = $value['exercicio'];
                $arDadosHistorico[$key]['cod_norma']        = $value['cod_norma'];
            }

            Sessao::write('arDadosHistorico',$arDadosHistorico);

            $stFiltro = ' WHERE cod_logradouro = '.$request->get('inCodigoLogradouro');
            $obTCIMTrecho = new TCIMTrecho();
            $obTCIMTrecho->retornaSomaExtensao($rsRecordSet, $stFiltro);
            if ($rsRecordSet->getNumLinhas > 0) {
                $stJs .= " jq_('#stExtensao').html(".$rsRecordSet->getCampo('extensao_total')."); ";
            }
        break;
        case 'consultar':
            $obRCIMLogradouro = new RCIMLogradouro;    
            $obRCIMLogradouro->setCodigoUF( $request->get("inCodUF") );
            $obRCIMLogradouro->setCodigoMunicipio( $request->get("inCodMunicipio") );
            $obRCIMLogradouro->setCodigoLogradouro( $request->get("inCodigoLogradouro") );
            $obRCIMLogradouro->listarHistoricoLogradouros( $rsLista, $boTransacao, "" );    

            foreach ($rsLista->getElementos() as $key => $value) {
                $arDadosHistorico[$key]['inId']             = $key;
                $arDadosHistorico[$key]['sequencial']       = $value['sequencial'];
                $arDadosHistorico[$key]['descricao_norma']  = $value['descricao_norma'];
                $arDadosHistorico[$key]['nome_anterior']    = $value['nome_anterior'];
                $arDadosHistorico[$key]['dt_inicio']        = $value['dt_inicio'];
                $arDadosHistorico[$key]['dt_fim']           = $value['dt_fim'];
                $arDadosHistorico[$key]['exercicio']        = $value['exercicio'];
                $arDadosHistorico[$key]['cod_norma']        = $value['cod_norma'];
            }

            Sessao::write('arDadosHistorico',$arDadosHistorico);
            
            $stFiltro = ' WHERE cod_logradouro = '.$request->get('inCodigoLogradouro');
            $obTCIMTrecho = new TCIMTrecho();
            $obTCIMTrecho->retornaSomaExtensao($rsRecordSet, $stFiltro);
            if ($rsRecordSet->getNumLinhas > 0) {
                $stJs .= " jq_('#stExtensao').html('".$rsRecordSet->getCampo('extensao_total')."'); ";
            }
        break;

    }

    return $stJs;
}

function carregaBairroCEP()
{
    GLOBAL $request;
    $obRCIMLogradouro = new RCIMLogradouro;
    $obRCIMLogradouro->setCodigoUF            ( $request->get("inCodigoUF")         );
    $obRCIMLogradouro->setCodigoLogradouro    ( $request->get("inCodigoLogradouro") );
    $obRCIMLogradouro->listarBairroLogradouro ( $rsBairrosLogradouro );
    $obRCIMLogradouro->listarCEP              ( $rsCEPLogradouro     );

    $arBairrosSessao = $rsBairrosLogradouro->getElementos() ? $rsBairrosLogradouro->getElementos() : array();
    $arCepSessao     = $rsCEPLogradouro->getElementos() ? $rsCEPLogradouro->getElementos() : array();

    Sessao::write('bairros', $arBairrosSessao);
    Sessao::write('cep'    , $arCepSessao);
    
    return true;
}

function validaInclusaoLista($arDados)
{
    $boValida = true;
    foreach ($arDados as $key => $value) {
        if ($boValida == true) {
            if ( $_REQUEST['inCodNormaHistorico'] == $value['cod_norma'] &&
                     $_REQUEST['stDataInicialHistorico'] == $value['dt_inicio'] &&
                     $_REQUEST['stDataFinalHistorico'] == $value['dt_fim'] &&
                     $_REQUEST['stNomeLogradouroAnterior'] == $value['nome_anterior']
                    ) {
                        $boValida = false;
                        SistemaLegado::executaFrameOculto("alertaAviso('Não foi possível incluir porque o registro já existe na lista!','form','erro','".Sessao::getId()."','../');");
            }else{
                if ( $_REQUEST['stDataFinalHistorico'] == '' ) {
                    if($_REQUEST['stDataFinalHistorico'] == $value['dt_fim']) {
                        $stMensagem = "A data final deve ser preenchida.";
                        $boValida = false;
                    }
                } else {
                    if ( $value['dt_fim'] != '') {
                        if (SistemaLegado::comparaDatas($_REQUEST['stDataFinalHistorico'],$value['dt_inicio'],false)) {
                            if (!SistemaLegado::comparaDatas($_REQUEST['stDataInicialHistorico'],$value['dt_fim'],false)) {
                                $stMensagem = "A data inicial deve ser maior que a data final dos registros cadastrados"; 
                                $boValida = false;
                            }
                        }else{
                            if (!SistemaLegado::comparaDatas($_REQUEST['stDataFinalHistorico'],$value['dt_inicio'],true)) {
                                $boValida = true;
                            }else{
                                $stMensagem = "A data final deve ser menor que a data inicial dos registros cadastrados"; 
                                $boValida = false;
                            }
                        }
                    }
                }
            }
        }//end of IF
    }

    SistemaLegado::executaFrameOculto("alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."','../');");
    return $boValida;
}

function limparHistoricoLista()
{
    $stJs  = "jq_('#stNomeLogradouroAnterior').val(''); \n";
    $stJs .= "jq_('#inCodNormaHistorico').val(''); \n";
    $stJs .= "jq_('#stNormaHistorico').html('&nbsp;'); \n";
    $stJs .= "jq_('#stDataInicialHistorico').val(''); \n";
    $stJs .= "jq_('#stDataFinalHistorico').val(''); \n";
    return $stJs ;
}

function ordenaArrayDados(&$arDadosHistorico)
{
    usort($arDadosHistorico, function ($a, $b){
        return strcmp(SistemaLegado::dataToSql($a['dt_inicio']), SistemaLegado::dataToSql($b['dt_inicio']));
    });
}

// SELECIONA ACAO
switch ($request->get("stCtrl")) {
    case "incluirNovoBairro":

        Sessao::write('acao'  ,"784");
        Sessao::write('modulo',  "0");

        if (!$request->get( "stNovoBairro") || !$request->get( "inCodUF") || !$request->get( "inCodMunicipio")) {
            $js = " alertaAviso('Dados do lograoduro estão incompletos!','form','erro','".Sessao::getId()."', '../');\n";
            sistemaLegado::executaFrameOculto($js);
            exit;
        }

        $obRCIMBairro->setNomeBairro      ( $request->get( "stNovoBairro") );
        $obRCIMBairro->setCodigoUF        ( $request->get( "inCodUF") );
        $obRCIMBairro->setCodigoMunicipio ( $request->get( "inCodMunicipio") );

        $obRCIMBairro->incluirBairro();

        $js .= "f.stNovoBairro.value=''; \n";
        $js .= "f.inCodigoBairro.value=''; \n";
        $js .= "limpaSelect(f.inCodBairro,0); \n";
        $js .= "f.inCodBairro[0] = new Option('Selecione','', 'selected');\n";

        if ($request->get("inCodMunicipio")) {
            unset( $obRCIMBairro );
            $obRCIMBairro = new RCIMBairro;
            $obRCIMBairro->setCodigoMunicipio( $request->get("inCodMunicipio") );
            $obRCIMBairro->setCodigoUF( $request->get("inCodUF") );
            $obRCIMBairro->listarBairros ( $rsBairros );

            $inContador = 1;
        } else {
            $rsBairros = new RecordSet;
        }
        while ( !$rsBairros->eof() ) {
            $inCodBairro = $rsBairros->getCampo( "cod_bairro" );
            $stNomBairro = $rsBairros->getCampo( "nom_bairro" );
            $js .= "f.inCodBairro.options[$inContador] = new Option('".addslashes($stNomBairro)."','".$inCodBairro."'); \n";
            $inContador++;
            $rsBairros->proximo();
        }

        sistemaLegado::executaFrameOculto($js);
        break;

    case "preencheMunicipio":
        $js .= "f.inCodigoBairro.value=''; \n";
        $js .= "limpaSelect(f.inCodBairro,0); \n";
        $js .= "f.inCodBairro[0] = new Option('Selecione','', 'selected');\n";

        $js .= "f.inCodigoMunicipio.value=''; \n";
        $js .= "limpaSelect(f.inCodMunicipio,0); \n";
        $js .= "f.inCodMunicipio[0] = new Option('Selecione','', 'selected');\n";

        if ($request->get("inCodigoUF")) {
            $obRCIMBairro->setCodigoUF( $request->get("inCodigoUF") );
            $obRCIMBairro->listarMunicipios( $rsMunicipios );

            $inContador = 1;
            while ( !$rsMunicipios->eof() ) {
                $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
                $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
                $js .= "f.inCodMunicipio.options[$inContador] = new Option('".addslashes($stNomMunicipio)."','".$inCodMunicipio."'); \n";
                $inContador++;
                $rsMunicipios->proximo();
            }
        }

        if ($request->get("stLimpar") == "limpar") {
            $js .= "f.inCodigoMunicipio.value='".$request->get("inCodigoMunicipio")."'; \n";
            $js .= "f.inCodMunicipio.options[".$request->get("inCodigoMunicipio")."].selected = true; \n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;

    case "preencheBairro":
        $js .= "f.inCodigoBairro.value=''; \n";
        $js .= "limpaSelect(f.inCodBairro,0); \n";
        $js .= "f.inCodBairro[0] = new Option('Selecione','', 'selected');\n";
        if ($_POST["inCodMunicipio"]) {
            $obRCIMBairro->setCodigoMunicipio( $request->get("inCodMunicipio") );
            $obRCIMBairro->setCodigoUF( $request->get("inCodUF") );
            $obRCIMBairro->listarBairros ( $rsBairros );
            $inContador = 1;
        } else {
            $rsBairros = new RecordSet;
        }
        while ( !$rsBairros->eof() ) {
            $inCodBairro = $rsBairros->getCampo( "cod_bairro" );
            $stNomBairro = $rsBairros->getCampo( "nom_bairro" );
            $js .= "f.inCodBairro.options[$inContador] = new Option('".addslashes($stNomBairro)."','".$inCodBairro."'); \n";
            $inContador++;
            $rsBairros->proximo();
        }

        sistemaLegado::executaFrameOculto($js);
    break;

    case "incluirBairro":

        $obRCIMBairro = new RCIMBairro;
        $arBairros = $arTmpBairro = array ();

        $inCodigoMunicipio = $request->get("inCodigoMunicipio") ? $request->get("inCodigoMunicipio") : $sessao["cod_municipio"];
        $inCodigoUF = $request->get("inCodigoUF") ? $request->get("inCodigoUF") : $sessao["cod_uf"];

        $obRCIMBairro->setCodigoBairro    ( $request->get("inCodigoBairro") );
        $obRCIMBairro->setCodigoMunicipio ( $inCodigoMunicipio );
        $obRCIMBairro->setCodigoUF        ( $inCodigoUF );
        $obErro = $obRCIMBairro->consultarBairro();

        if ( !$obErro->ocorreu() ) {
            $arBairros["nom_bairro"]    = $obRCIMBairro->getNomeBairro();
            $arBairros["cod_bairro"]    = $request->get("inCodigoBairro");
            $arBairros["cod_municipio"] = $request->get("inCodigoMunicipio");
            $arBairros["cod_uf"]        = $request->get("inCodigoUF");

            $stInsere = false;
            $arBairrosSessao = Sessao::read('bairros');
            if ($arBairrosSessao) {
                $inCountSessao = count ($arBairrosSessao);
            } else {
                $inCountSessao = 0;
                $stInsere = true;
            }

            for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                if ($arBairrosSessao[$iCount]["cod_bairro"] == $arBairros["cod_bairro"]) {
                    $stInsere = false;
                    $iCount = $inCountSessao;
                } else {
                    $stInsere = true;
                }
            }
            if ($stInsere) {
                if ($arBairrosSessao) {
                    $inLast = count ($arBairrosSessao);
                } else {
                    $inLast = 0;
                    $arBairrosSessao = array ();
                    Sessao::write('bairros', $arBairrosSessao);
                }
                $arBairrosSessao[$inLast]["cod_bairro"]     = $arBairros["cod_bairro"];
                $arBairrosSessao[$inLast]["nom_bairro"]     = $arBairros["nom_bairro"];
                $arBairrosSessao[$inLast]["cod_municipio"]  = $arBairros["cod_municipio"];
                $arBairrosSessao[$inLast]["cod_uf"]         = $arBairros["cod_uf"];

                Sessao::write('bairros', $arBairrosSessao);
                montaListaBairro ( $arBairrosSessao );
            } else {
                $js = " mensagem += \"@Bairro já informado! (".$obRCIMBairro->getNomeBairro().")\";\n";
                $js.= " alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
                sistemaLegado::executaFrameOculto($js);
            }
        } else {
            $js = " mensagem += \"@".$obErro->getDescricao()."!\";\n";
            $js.= " alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
            sistemaLegado::executaFrameOculto($js);
        }
    break;

    case "excluirBairro":

        $arTmpBairro = array ();
        $inCountArray = 0;
        $arBairrosSessao = Sessao::read('bairros');
        $inCountSessao = count ( $arBairrosSessao );

        if ( $request->get('stAcao') == 'alterar' ){
            for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
    
                if ($arBairrosSessao[$inCount][ "cod_bairro" ] == $request->get("inIndice") ) {
    
                    //VERIFICA SE O BAIRRO ESTA VINCULADO A ALGUM REGISTRO DOMICILIO INFORMADO
                    $inCodBairroAtual       = $arBairrosSessao[$inCount][ "cod_bairro" ];
                    $inCodLogradouroAtual   = $arBairrosSessao[$inCount][ "cod_logradouro" ];
                    $inCodMunicipioAtual    = $arBairrosSessao[$inCount][ "cod_municipio" ];
                    $inCodUFAtual           = $arBairrosSessao[$inCount][ "cod_uf" ];
                    $inCodLogradouro        = (null !== $request->get('inCodLogradouro')) ? $request->get('inCodLogradouro') : $request->get('inCodigoLogradouro');
                    
                    include_once( CAM_GT_CEM_MAPEAMENTO."TCEMDomicilioInformado.class.php" );
                    $obTCEMDomicilioInformado = new TCEMDomicilioInformado;

                    $stFiltro  =" cod_logradouro = ". $inCodLogradouro." AND\n";
                    $stFiltro .=" cod_bairro = ". $inCodBairroAtual." AND\n";
                    $stFiltro .=" cod_municipio = ". $inCodMunicipioAtual." AND\n";
                    $stFiltro .=" cod_uf = ". $inCodUFAtual." \n";
    
                    $stFiltro = " WHERE ".$stFiltro;
                    $stOrdem = " ";
                    $obTCEMDomicilioInformado->recuperaTodos ( $rsRegistos, $stFiltro, $stOrdem, $boTransacao );
    
                    if ( $rsRegistos->getNumLinhas() > 0 ) {
                        $mensagem = "Bairro utilizado por Inscrição Econômica em seu endereço de <b>DOMICÍLIO FISCAL</b>";
                        $js.= " alertaAviso('". $mensagem ."','form','erro','".Sessao::getId()."', '../'); \n";
                        sistemaLegado::executaFrameOculto($js);
                    }
                }
            }
        }

        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {

            if ($arBairrosSessao[$inCount][ "cod_bairro" ] != $request->get( "inIndice")) {

                $arTmpBairro[$inCountArray]["cod_bairro"]    = $arBairrosSessao[$inCount][ "cod_bairro" ];
                $arTmpBairro[$inCountArray]["nom_bairro"]    = $arBairrosSessao[$inCount][ "nom_bairro" ];
                $arTmpBairro[$inCountArray]["cod_municipio"] = $arBairrosSessao[$inCount][ "cod_municipio" ];
                $arTmpBairro[$inCountArray]["cod_uf"]        = $arBairrosSessao[$inCount][ "cod_uf" ];
                $inCountArray++;

            }

        }
        $arBairrosSessao = array();
        $arBairrosSessao = $arTmpBairro;
        Sessao::write('bairros', $arBairrosSessao);

        montaListaBairro ( $arBairrosSessao );

    break;

    case "incluirCEP":
        $inCEP = explode ("-", $request->get( "inCEP"));
        $inCEP = $inCEP[0].$inCEP[1];

        $arCEP = $arTmpBairro = array ();
        $arCEP[ "cep"         ] = $inCEP;
        $arCEP[ "num_inicial" ] = $request->get( "inInicial");
        $arCEP[ "num_final"   ] = $request->get( "inFinal");
        if ($request->get( "boNumeracao") == "Pares") {
            $arCEP[ "par"       ] = "true";
            $arCEP[ "impar"     ] = "false";
            $arCEP[ "numeracao" ] = "Pares";
        } elseif ($request->get("boNumeracao") == "Ímpares") {
            $arCEP[ "impar"     ] = "true";
            $arCEP[ "par"       ] = "false";
            $arCEP[ "numeracao" ] = "&Iacute;mpares";
        } else {
            $arCEP[ "impar"     ] = "true";
            $arCEP[ "par"       ] = "true";
            $arCEP[ "numeracao" ] = "Todos";
        }

        $stInsere = false;
        $arCepSessao = Sessao::read('cep');
        if ($arCepSessao) {
            $inCountSessao = count ($arCepSessao);
        } else {
            $inCountSessao = 0;
            $stInsere = true;
        }

        for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
            if ($arCepSessao[$iCount]["cep"] == $arCEP["cep"]) {
                $stInsere = false;
                $iCount = $inCountSessao;
            } else {
                $stInsere = true;
            }
        }
        if ($stInsere) {
            if ($arCepSessao) {
                $inLast = count ($arCepSessao);
            } else {
                $inLast = 0;
                $arCepSessao = array ();
                Sessao::write('cep', $arCepSessao);
            }
            $arCepSessao[$inLast]["cep"        ] = $arCEP["cep"        ];
            $arCepSessao[$inLast]["num_inicial"] = $arCEP["num_inicial"];
            $arCepSessao[$inLast]["num_final"  ] = $arCEP["num_final"  ];
            $arCepSessao[$inLast]["par"        ] = $arCEP["par"        ];
            $arCepSessao[$inLast]["impar"      ] = $arCEP["impar"      ];
            $arCepSessao[$inLast]["numeracao"  ] = $arCEP["numeracao"  ];
            Sessao::write('cep', $arCepSessao);

            montaListaCEP ( $arCepSessao );
            exit (0);
        } else {
            $js = " mensagem += \"@CEP já informado! (".$request->get( "inCEP").")\";\n";
            $js .= "f.inCEP.value=''; \n";
            $js .= "f.inInicial.value=''; \n";
            $js .= "f.inFinal.value=''; \n";
            $js .= "f.boNumeracao[0].checked = true; \n";
            $js.= " alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
            sistemaLegado::executaFrameOculto($js);
            exit(0);
        }
    break;

    case "excluirCEP":
        $arTmpCEP = array ();
        $arCepSessao = Sessao::read('cep');
        $inCountSessao = count ($arCepSessao);
        $inCountArray = 0;
        $inCEP = str_replace("-", "", $request->get('inIndice'));
        
        $obRCIMLogradouro = new RCIMLogradouro;

        if ( $request->get('stAcao') == 'alterar' ) {
            $inCodLogradouro = $request->get('inCodigoLogradouro');
            $stFiltro  = " WHERE cod_logradouro = ".$inCodLogradouro;
            $stFiltro .= " AND cep = '".$inCEP."'";
            $obRCIMLogradouro->obTCEPLogradouro->recuperaRelacionamentoCGMLogradouro($rsCGMLogradouro, $stFiltro, "", $boTransacao);
        
            if ( $rsCGMLogradouro->getNumLinhas() > 0 ) {
                $mensagem = "Exclusão não permitida pois o CEP está sendo utilizado.";
                $js.= " alertaAviso('". $mensagem ."','form','erro','".Sessao::getId()."', '../'); \n";
                sistemaLegado::executaFrameOculto($js);
                exit();
            }else{
                $obRCIMLogradouro->excluirCEPLogradouro($inCEP,$boTransacao);
            }
        }
        
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            //if ($sessao->transf6[ "cep" ][$inCount][ "cep" ] != $request->get( "inIndice")])
            $cepSessao = substr($request->get( "inIndice"),0,5).substr($request->get( "inIndice"),6,3);
            if ($arCepSessao[$inCount][ "cep" ] != $cepSessao) {
                $arTmpCEP[$inCountArray]["cep"]         = $arCepSessao[$inCount][ "cep" ];
                $arTmpCEP[$inCountArray]["num_inicial"] = $arCepSessao[$inCount][ "num_inicial" ];
                $arTmpCEP[$inCountArray]["num_final"]   = $arCepSessao[$inCount][ "num_final" ];
                $arTmpCEP[$inCountArray]["numeracao"]   = $arCepSessao[$inCount][ "numeracao" ];
                // Esperando campo na tabela de CEP_LOGRADOURO
                $inCountArray++;
            }
        }
        
        $arCepSessao = array();
        $arCepSessao = $arTmpCEP;
        
        Sessao::write('cep', $arCepSessao);

        montaListaCEP ( $arCepSessao );
    break;

    case 'limparListas' :        
        $stJs .= "jq_('#inCodigoTipo').val('');\n";
        $stJs .= "jq_('#stNomeLogradouro').val('');\n";
        $stJs .= "jq_('#inCodigoBairro').val('');\n";
        $stJs .= montaListaBairro ( Sessao::write('bairros', array()), true);
        $stJs .= montaListaCEP ( Sessao::write('cep', array()) , true);
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case 'preencheInner':
        
        carregaBairroCEP();
        $arBairrosSessao = Sessao::read('bairros');
        $arCepSessao     = Sessao::read('cep');

        if ($arBairrosSessao) {
            $stJs = montaListaBairro ( $arBairrosSessao , true, true);
        }
        if ($arCepSessao) {
            $stJs .= montaListaCEP ( $arCepSessao, true, true);
        }
        
        $stJs .= carregaDados();

        $stJs .= montaListaHistorico( Sessao::read("arDadosHistorico") );

        SistemaLegado::executaFrameOculto($stJs);
    break;

    case 'preencheInnerConsultar':
        carregaBairroCEP();
        $arBairrosSessao = Sessao::read('bairros');
        $arCepSessao     = Sessao::read('cep');
        
        if ($arBairrosSessao) {
            $stJs = montaListaBairro ( $arBairrosSessao, true, false);
        }
        if ($arCepSessao) {
            $stJs .= montaListaCEP ( $arCepSessao, true, false);
        }

        $stJs .= carregaDados();

        $stJs .= montaListaHistorico(Sessao::read("arDadosHistorico"));

        SistemaLegado::executaFrameOculto($stJs);
    break;


    case 'IniciaSessions':    
        $arBairrosSessao = array();
        $arCepSessao     = array();
        Sessao::write('bairros', $arBairrosSessao);
        Sessao::write('cep'    , $arCepSessao);

        $stJs  = montaListaBairro   ( $arBairrosSessao , true, true);
        $stJs .= montaListaCEP      ( $arCepSessao     , true, true);
        $stJs .= montaListaHistorico( $arDadosHistorico );
        $stJs .= carregaDados();

        SistemaLegado::executaFrameOculto($stJs);
    break;

    case 'verificaCodigoLogradouro':
        $inCodLogradouro = $request->get('inCodLogradouro');
        if ( $request->get('inCodNorma') == '' || $request->get('stDataInicial') == '') {
            $stMensagem = "Preencha todos os campos obrigatórios";
        }
        if ( $stMensagem == '') {
            if (empty($inCodLogradouro)) {
                $stJs .= "f.submit();";
                SistemaLegado::executaFrameOculto($stJs);
                break;
            }

            $obRCIMLogradouro = new RCIMLogradouro;
            $obRCIMLogradouro->setCodigoLogradouro($inCodLogradouro);
            $obRCIMLogradouro->consultarLogradouro($rsLogradouro);

            if ($rsLogradouro->getNumLinhas() > 0) {
                $obTLogradouro= new TLogradouro();
                $obTLogradouro->proximoCod($inProxCodLogradouro);
    
                $stJs .= "if (confirm('O Código ".$inCodLogradouro." já foi utilizado. Deseja utilizar próximo código: ".$inProxCodLogradouro."')) { f.submit(); } else { false; };";
            } else {
                $stJs .= " jq_('#stDescricaoNorma').val(jq_('#stNorma').html()); f.submit();";
            }    
        }else{
            $stJs = "alertaAviso('".$stMensagem."','n_incluir','aviso','".Sessao::getId()."'); ";
        }

        SistemaLegado::executaFrameOculto($stJs);
    break;

    case 'limparHistoricoLista':
        $stJs = limparHistoricoLista();
        
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case 'incluirHistoricoLista':
        $arDadosHistorico = Sessao::read('arDadosHistorico');
        $inProx = count($arDadosHistorico);
        $boValida = true;
        if( $inProx > 0 ){
            $boValida = validaInclusaoLista($arDadosHistorico);
        }
        
        if ($boValida) {
            $stDescricaoLei = $_REQUEST['inCodNormaHistorico'].' - '.$_REQUEST['stDescricaoNormaHistorico'];
            
            $arDadosHistorico[$inProx]['inId']             = $inProx;
            $arDadosHistorico[$inProx]['sequencial']       = '';
            $arDadosHistorico[$inProx]['descricao_norma']  = $stDescricaoLei;
            $arDadosHistorico[$inProx]['nome_anterior']    = $request->get('stNomeLogradouroAnterior');
            $arDadosHistorico[$inProx]['dt_inicio']        = $request->get('stDataInicialHistorico');
            $arDadosHistorico[$inProx]['dt_fim']           = $request->get('stDataFinalHistorico');
            $arDadosHistorico[$inProx]['exercicio']        = Sessao::getExercicio();
            $arDadosHistorico[$inProx]['cod_norma']        = $request->get('inCodNormaHistorico');
            
            ordenaArrayDados($arDadosHistorico);

            Sessao::write('arDadosHistorico',$arDadosHistorico);
            $stJs  = montaListaHistorico($arDadosHistorico);
            $stJs .= " jq_('#stNomeLogradouroAnterior').val(''); \n";
            $stJs .= " jq_('#inCodNormaHistorico').val(''); \n";
            $stJs .= " jq_('#stNormaHistorico').html('&nbsp;'); \n";
            $stJs .= " jq_('#stDataInicialHistorico').val(''); \n";
            $stJs .= " jq_('#stDataFinalHistorico').val(''); \n";
        }

        SistemaLegado::executaFrameOculto($stJs);
    break;

    case 'alterarHistoricoLista':
        $arDadosHistorico = Sessao::read('arDadosHistorico');

        foreach ($arDadosHistorico as $key => $value) {
            if ($_REQUEST['inId'] == $value['inId']) {
                $stJs  = " jq_('#stNomeLogradouroAnterior').val('".$value['nome_anterior']."'); \n";
                $stJs .= " jq_('#inCodNormaHistorico').val('".$value['cod_norma']."'); \n";
                $stJs .= " jq_('#inCodNormaHistorico').blur(); \n";
                $stJs .= " jq_('#stDataInicialHistorico').val('".$value['dt_inicio']."'); \n";
                $stJs .= " jq_('#stDataFinalHistorico').val('".$value['dt_fim']."'); \n";
                $stJs .= " jq_('#btIncluir').val('Alterar'); \n";
                $stJs .= " jq_('#btIncluir').attr('onclick','if ( validaCamposLista() ){ manterHistorico(\'alterarListaHistorico\');}'); \n";
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
    
    break;
    
    case 'excluirHistoricoLista':
        $arDadosHistorico = Sessao::read('arDadosHistorico');
                
        foreach ($arDadosHistorico as $key => $value) {
            if ($_REQUEST['inId'] != $value['inId']) {
                $arTmp[] = $value;
            }
        }

        $arDadosHistorico = array();
        $arDadosHistorico[] = $arTmp;
        
        Sessao::write('arDadosHistorico',$arTmp);
        
        $stJs .= montaListaHistorico($arTmp);
        $stJs .= limparHistoricoLista();

        SistemaLegado::executaFrameOculto($stJs);

    break;

    case 'alterarListaHistorico':
        //validação para os dados
        $arDadosHistorico = Sessao::read('arDadosHistorico');
        
        foreach ($arDadosHistorico as $key => $value) {
            if ($_REQUEST['inId'] == $value['inId']) {
                $stDescricaoLei = $_REQUEST['inCodNormaHistorico'].' - '.$_REQUEST['stDescricaoNormaHistorico'];
                $arDadosHistorico[$key]['inId']             = $value['inId'];
                $arDadosHistorico[$key]['descricao_norma']  = $stDescricaoLei;
                $arDadosHistorico[$key]['nome_anterior']    = $request->get('stNomeLogradouroAnterior');
                $arDadosHistorico[$key]['dt_inicio']        = $request->get('stDataInicialHistorico');
                $arDadosHistorico[$key]['dt_fim']           = $request->get('stDataFinalHistorico');
                $arDadosHistorico[$key]['exercicio']        = Sessao::getExercicio();
                $arDadosHistorico[$key]['cod_norma']        = $request->get('inCodNormaHistorico');
            }
        }

        ordenaArrayDados($arDadosHistorico);

        Sessao::write('arDadosHistorico',$arDadosHistorico);

        $stJs .= " jq_('#btIncluir').val('Incluir'); \n";
        $stJs .= " jq_('#btIncluir').attr('onclick','if ( validaCamposLista() ){ manterHistorico(\'incluirHistoricoLista\'); }'); \n";
        $stJs .= montaListaHistorico($arDadosHistorico);
        $stJs .= limparHistoricoLista();

        SistemaLegado::executaFrameOculto($stJs);
    break;

}

?>
