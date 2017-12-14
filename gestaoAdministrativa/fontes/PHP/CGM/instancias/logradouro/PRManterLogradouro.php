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
    * Página de processamento para o cadastro de logradouro
    * Data de Criação   : 14/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
                             Gustavo Passos Tourinho
                             Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: PRProcurarLogradouro.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"       );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterLogradouro" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?&stLink=".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$obRCIMLogradouro = new RCIMLogradouro;
$boTransacao = new Transacao();

switch ( $request->get('stAcao') ) {
    case "incluir":
        $obErro = new Erro;
        $obRCIMLogradouro->setCodigoLogradouro( $request->get("inCodLogradouro")       );
        $obRCIMLogradouro->consultarLogradouro($rsLogradouro);

        if ($rsLogradouro->getNumLinhas() > 0) {
            $obTLogradouro= new TLogradouro();
            $obTLogradouro->proximoCod($inProxCodLogradouro);
            $obRCIMLogradouro->setCodigoLogradouro($inProxCodLogradouro);
            $stJs = "f.inCodLogradouro.value=$inProxCodLogradouro";
            sistemaLegado::executaIFrameOculto($stJs);
        }

        $arBairrosSessao  = Sessao::read('bairros');
        $arCepSessao      = Sessao::read('cep');
        $arDadosHistorico = Sessao::read('arDadosHistorico');
        //Adicionar dados da inclusao do logradouro no array de historico geral
        $arDadosIncluir['inId'] = count($arDadosHistorico);
        $arDadosIncluir['sequencial'] = '';
        $arDadosIncluir['descricao_norma'] = $request->get('stDescricaoNorma');
        //adicionando nome da inclusao no campos de nome_anterior do historico pq faz parte de um todo
        $arDadosIncluir['nome_anterior'] = $request->get('stNomeLogradouro');
        $arDadosIncluir['dt_inicio'] =  $request->get('stDataInicial');
        $arDadosIncluir['dt_fim'] =  $request->get('stDataFinal');
        $arDadosIncluir['exercicio'] = Sessao::getExercicio();
        $arDadosIncluir['cod_norma'] =  $request->get('inCodNorma');
        $arDadosHistorico[] = $arDadosIncluir;

        if ( count ($arBairrosSessao) < 1 ) {

            $obErro->setDescricao ("Deve-se informar o bairro do novo Logradouro");

        } elseif ( count ($arCepSessao) < 1 ) {

            $obErro->setDescricao ("Deve-se informar um CEP para o novo Logradouro");

        }else if ( count($arDadosHistorico) < 1 ){
            $obErro->setDescricao ("Deve-se informar um registro de histórico para o novo Logradouro");
        
        } else {
            $obRCIMLogradouro->setCEP ( $arCepSessao );
            $obErro = $obRCIMLogradouro->addBairro( $arBairrosSessao, $boTransacao );
        }

        if (!$request->get('inCodUF')) {
            $obErro->setDescricao('Deve-se informar o Estado do novo Logradouro');
        }

        if (!$request->get('inCodMunicipio')) {
            $obErro->setDescricao('Deve-se informar o Município do novo Logradouro');
        }

        if (!$request->get('inCodTipo')) {
            $obErro->setDescricao('Deve-se informar o tipo do novo Logradouro');
        }

        if (!$request->get('stNomeLogradouro')) {
            $obErro->setDescricao('Deve-se informar o nome do novo Logradouro');
        }

        if ( !$obErro->ocorreu() ) {
            $obRCIMLogradouro->setCodigoUF        ( $request->get("inCodUF")          );
            $obRCIMLogradouro->setCodigoMunicipio ( $request->get("inCodMunicipio")   );
            $obRCIMLogradouro->setCodigoTipo      ( $request->get("inCodTipo")        );
            $obRCIMLogradouro->setNomeLogradouro  ( trim( $request->get("stNomeLogradouro") ) );
            $obRCIMLogradouro->setDadosHistorico  ( $arDadosHistorico );

            $obErro = $obRCIMLogradouro->incluirLogradouro();
        }

        $inProxCodLogradouro = ($inProxCodLogradouro == '') ? $request->get('inCodLogradouro') : $inProxCodLogradouro;

        if ( !$obErro->ocorreu() ) {            
            SistemaLegado::alertaAviso($pgForm."?stAcao=".$request->get('stAcao'),"Logradouro: ".$inProxCodLogradouro." - ".$request->get("stNomeLogradouro"),"incluir","aviso",Sessao::getId(),"../");
        } else {            
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obErro = new Erro;
        $arDadosHistorico = Sessao::read('arDadosHistorico');
        $arDadosHistorico = ($arDadosHistorico == '') ? array() : $arDadosHistorico;
        $arTmp = end($arDadosHistorico);
        foreach ($arDadosHistorico as $key => $value) {
            if ( $value['inId'] == $arTmp['inId']) {
                $arDadosHistorico[$key]['descricao_norma'] = $request->get('stDescricaoNorma');
                $arDadosHistorico[$key]['nome_anterior']   = $request->get('stNomeLogradouro');
                $arDadosHistorico[$key]['dt_inicio']       = $request->get('stDataInicial');
                $arDadosHistorico[$key]['dt_fim']          = $request->get('stDataFinal');
                $arDadosHistorico[$key]['exercicio']       = Sessao::getExercicio();
                $arDadosHistorico[$key]['cod_norma']       = $request->get('inCodNorma');        
            }
        }

        $obRCIMLogradouro->setCodigoLogradouro ( $request->get("inCodigoLogradouro") );
        $obRCIMLogradouro->setCodigoUF         ( $request->get('inCodUF')            );
        $obRCIMLogradouro->setCodigoMunicipio  ( $request->get('inCodMunicipio')     );
        $obRCIMLogradouro->setNomeLogradouro   ( $request->get("stNomeLogradouro") );
        $obRCIMLogradouro->setCodigoTipo       ( $request->get("inCodigoTipo")     );
        $obRCIMLogradouro->setCEP              ( Sessao::read('cep')               );
        $obRCIMLogradouro->setDadosHistorico   ( $arDadosHistorico  );

        $obErro = $obRCIMLogradouro->addBairro( Sessao::read('bairros'), $boTransacao );
        if ( $obErro->ocorreu() ) {
            break;
        }

        if ( !$obErro->ocorreu() ) {
           $obErro = $obRCIMLogradouro->alterarLogradouro();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.Sessao::read('link'),"Logradouro: ".$request->get("inCodigoLogradouro")." - ".$request->get('stNomeLogradouro'),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList.Sessao::read('link')."&stErro=".urlencode($obErro->getDescricao()),"" ,"n_alterar","erro", Sessao::getId(), "../");
        }
    break;

    case "excluir";
        $obRCIMLogradouro->setCodigoLogradouro ( $request->get("inCodigoLogradouro") );
        $obRCIMLogradouro->setCodigoMunicipio  ( $request->get("inCodigoMunicipio")  );
        $obRCIMLogradouro->setCodigoUF         ( $request->get("inCodigoUF")         );

        $obErro = $obRCIMLogradouro->excluirLogradouro();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.Sessao::read('link'),"Logradouro: ".$request->get("inCodigoLogradouro")." - ".$request->get("stNomeLogradouro") ,"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList.Sessao::read('link')."&stErro=".urlencode($obErro->getDescricao()),"" ,"n_excluir","aviso", Sessao::getId(), "../");
        }
    break;
    case "consultar";
        SistemaLegado::mudaFramePrincipal($pgList.Sessao::read('link'));
    break;
}

?>
