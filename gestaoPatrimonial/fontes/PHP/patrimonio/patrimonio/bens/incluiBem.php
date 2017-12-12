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
    * Inclusão de novos bens
    * Data de Criação   : 24/03/2003

    * @author Desenvolvedor Ricardo Lopes de Alencar

    * @ignore

    $Revision: 24643 $
    $Name$
    $Autor: $
    $Date: 2007-08-09 16:35:48 -0300 (Qui, 09 Ago 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.18  2007/08/09 19:35:48  rodrigo_sr
Bug#9822#

Revision 1.17  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.16  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.15  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';         //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';    //Inclui classe para inserir auditoria
include_once '../bens.class.php';                    //Inclui classe que controla os bens
include_once 'interfaceBens.class.php';              //Inclui classe que contém a interface html
include_once 'JSIncluiBem.js';                       //inlcui arquivo JS
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.06");
if (!isset($controle)) {
    $controle = 0;
}
switch ($controle) {

    //abre interface para inclusão de bens
    case 0:
      $html = new interfaceBens;
      $html->acaoMenu = $sessao->acao;
      $html->formCadastroBens( $HTTP_POST_VARS, $PHP_SELF, 0, 0, "incluir" );

    break;

    //chama metodo para incluir bem
    case 1:
        $erro = false;

        // verifica se o local informado eh valido
        $local = explode ( "/" , $codMasSetor );
        if (!($vetLocal = validaLocal($local[0],$local[1]))) {
            $erro = true;
            exibeAviso("Local informado inválido!(Código: $codMasSetor )","unica","erro");
            $js .= 'f.controle.value = "0" ;';
            executaFrameOculto($js);
        }

        // verifica se todos os atributos obrigatorios foram preenchdos
        if ( is_array($atributos) and $erro == false ) {
            foreach ($atributos as $codAtributo => $valorAtributo) {
                if ($valorAtributo == "") {
                    $erro = true;
                    exibeAviso("Todos os atributos devem ser preenchidos","unica","erro");
                    $js .= 'f.controle.value = "0" ;';
                    executaFrameOculto($js);
                }
            }
        }

        $teste = pegaDado("cod_entidade","orcamento.entidade","where exercicio = $exercicioEmpenho and cod_entidade = $codEntidade");
        if ($teste == null) {
            $erro = true;
            exibeAviso("A Entidade ($codEntidade) não está cadastrada no Exercício ($exercicioEmpenho)!","unica","erro");
            $js .= 'f.controle.value = "0" ;';
            executaFrameOculto($js);
        }

        //verifica se a data da aquisição do bem é anterior ou igual ao dia de hoje
//        if ($erro == false) {
//            $arDataAquisicao = explode( "/" , $dataAquisicao );
//            $dataAquisicaoCheck = $arDataAquisicao[2].$arDataAquisicao[1].$arDataAquisicao[0];
//            $dataHoje           = date( "Ymd" );
//            if ($dataHoje < $dataAquisicaoCheck) {
//                $erro = true;
//                exibeAviso("A data da aquisição é maior do que a data atual!( $dataAquisicao )","unica","erro");
//                $js .= 'f.controle.value = "0" ;';
//                executaFrameOculto($js);
//            }
//        }

    if ($numPlaca != "" && !comparaValor("num_placa", $numPlaca,"patrimonio.bem")) {
        exibeAviso("A placa ".$numPlaca." pertence a outro bem, por favor escolha outra placa","unica","erro");
        $erro = true;
    }

        if ($erro == false) {
            $codBem = pegaID( "cod_bem" , 'patrimonio.bem' );
            $bens   = new bens;

            if ($identificacao == 'S') {
                $identificacao = 'true';
            } else {
                $identificacao = 'false';
            }
            //die($numPlaca);
            if( $bens->incluirBem
                (
                    $codBem, $descricao, $detalhamento,$dataDepreciacao, $dataAquisicao, $dataDepreciacao, $dataGarantia,
                    $valorBem, $valorDepreciacao, $identificacao, $situacao, $descSituacao, $codMasSetor,
                    $exercicio, $fornecedor, $atributos, $codNatureza, $codGrupo, $codEspecie,
                    $codEmpenho, $numNotaFiscal, $exercicioEmpenho, $numPlaca, $codEntidade
                )
             ){
                $ok = true;
                //Insere auditoria

                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codBem);
                $audicao->insereAuditoria();

                //Exibe mensagem de confirmação
                exibeAviso("Bem: $codBem","incluir","aviso");

                // limpa alguns campos do formulario de insercao de bens que retornara preenchido
                // apos a insercao
                $js .= 'f.controle.value = "0" ;';
                $js .= 'f.situacao.value = "xxx" ;';
                $js .= 'f.codTxtSituacao.value = "" ;';
                $js .= 'f.descSituacao.value = "" ;';

                executaFrameOculto($js);

            } else {
                exibeAviso("Bem","n_incluir","erro");
            }
        }
    break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
?>
