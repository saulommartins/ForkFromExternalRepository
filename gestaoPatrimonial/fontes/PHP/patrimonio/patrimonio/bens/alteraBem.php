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
    * Seleciona e altera bens
    * Data de Criação   : 25/03/2003

    * @author Desenvolvedor Ricardo Lopes de Alencar

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.17  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.16  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.15  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php'; //Inclui classe para inserir auditoria
include_once '../bens.class.php'; //Inclui classe que controla os bens
include_once 'interfaceBens.class.php'; //Inclui classe que contém a interface html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once 'JSIncluiBem.js';

setAjuda("UC-03.01.06");
if(!isset($controle))
        $controle = 0;

switch ($controle) {

    // formulario de pesquisa
    case 0:
        include_once 'listarBens.php';
    break;

    // busca dados do bem e abre formulario preenchido com os dados selecionado
    case 1:
        //Verifica se o bem digitado está ativo
        if (SistemaLegado::pegaDado("cod_bem","patrimonio.bem_baixado","Where cod_bem = '".$codBem."' ")) {
           sistemaLegado::alertaAviso($PHP_SELF."?controle=0","Este bem está baixado! (Bem: ".$codBem.")","unica","aviso", Sessao::getId(), "");
            break;
        }

        //Verifica se o bem digitado existe
        if (SistemaLegado::pegaDado("cod_bem","patrimonio.bem","Where cod_bem = '".$codBem."' ")) {
            //Mostra os dados do bem
            $html = new interfaceBens;
                if (!isset($reload)) {
                    $bens = new bens;
                    $vetBens = $bens->pegaDados($codBem);
                    $html->formCadastroBens($vetBens,$PHP_SELF,$controle,0,"alterar");
                } else {
                    $html->formCadastroBens($HTTP_POST_VARS,$PHP_SELF,$controle,0,"alterar");
                }
        } else {
           sistemaLegado::alertaAviso($PHP_SELF,"Nenhum registro encontrado!","unica","aviso", Sessao::getId(), "");
        }

    break;

    // recarrega formulario após reload
    case 3:

        $html = new interfaceBens;
        $html->formCadastroBens($HTTP_POST_VARS,$PHP_SELF,$controle,0,"alterar");

    break;

    case 2:

        // verifica se o local informado eh valido
        $local = explode ("/", $codMasSetor);
        if (!($vetLocal = validaLocal($local[0],$local[1]))) {
            $erro = 1;
            exibeAviso("O local informado é inválido","unica","erro");
            $js .= 'f.controle.value = "0" ;';
            executaFrameOculto($js);
        }

        // verifica se todos os atributos obrigatorios foram preenchdos
        if (is_array($atributos)) {
            $erro = "";
            foreach ($atributos as $codAtributo=>$valorAtributo) {
                if ($valorAtributo == "") {
                    $erro = 1;
                    exibeAviso("Todos os atributos devem ser preenchidos","unica","erro");
                    $js .= 'f.controle.value = "0" ;';
                    executaFrameOculto($js);
                    //exibeAviso("Os campos de atributos são obrigatórios","unica","aviso");
                }
            }
        }
    //Verifica se já existe o registro a ser incluido
    if ($numPlaca != "" && !comparaValor("num_placa", $numPlaca,"patrimonio.bem","And cod_bem <> ".$codBem." ")) {
       exibeAviso("A placa ".$numPlaca." pertence a outro bem, por favor escolha outra placa","unica","erro");
       $erro = true;
    }

        if (!$erro) {
            if ($identificacao == 'S') {
                $identificacao = 'true';
            } else {
                $identificacao = 'false';
            }

            $bens = new bens;
            if($bens->alterarBem($codBem, $descricao, $detalhamento, $dataAquisicao, $dataDepreciacao, $dataGarantia,
                            $valorBem, $valorDepreciacao, $identificacao, $situacao, $descSituacao, $codMasSetor, $exercicio, $fornecedor,
                            $atributos, $codNatureza, $codGrupo, $codEspecie, $codEmpenho,$numNotaFiscal, $exercicioEmpenho,
                            $numPlaca, $codEntidade)){
                //Insere auditoria
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codBem);
                $audicao->insereAuditoria();
                alertaAviso( $PHP_SELF."?ctrl_frm=2&pagina=".$sessao->filtro['pagina'], "Bem: $codBem", "alterar", "aviso", Sessao::getId() );
            } else {
                exibeAviso("bem $codBem","n_alterar","erro");
            }
        }
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html
?>
