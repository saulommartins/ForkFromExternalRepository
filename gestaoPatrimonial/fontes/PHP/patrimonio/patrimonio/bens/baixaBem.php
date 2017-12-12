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
    * Seleciona e baixa bens
    * Data de Criação   : 10/04/2003

    * @author Desenvolvedor Ricardo Lopes de Alencar

    * @ignore

    $Revision: 18992 $
    $Name$
    $Autor: $
    $Date: 2006-12-26 15:35:24 -0200 (Ter, 26 Dez 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.19  2006/12/26 17:35:24  hboaventura
Bug #7517#

Revision 1.18  2006/12/05 10:51:05  hboaventura
bug #7517#

Revision 1.17  2006/11/27 17:05:43  larocca
Bug #7517#

Revision 1.16  2006/11/20 13:04:32  bruce
Bug #7517#

Revision 1.15  2006/11/20 12:15:55  bruce
Bug #6920#

Revision 1.14  2006/10/13 09:09:57  larocca
Bug #6931#

Revision 1.13  2006/07/21 11:35:07  fernando
Inclusão do  Ajuda.

Revision 1.12  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.11  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php'; //Inclui classe para inserir auditoria
include_once '../bens.class.php'; //Inclui classe que controla os bens
include '../../../frota/frota/baixaVeiculos.class.php';
include_once 'interfaceBens.class.php'; //Inclui classe que contém a interface html
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

setAjuda("UC-03.01.06");
if(!isset($controle))
        $controle = 0;

switch ($controle) {

    case 0:
        $html = new interfaceBens;
        $html->formBaixaBem($HTTP_POST_VARS, $PHP_SELF,$controle,$tipoBaixa);
    break;

    //Baixa única
    case 1:

        $baixaVeiculos = new baixaVeiculos;
        $baixaVeiculos->codigoBem = $codBem;
        $audicao = new auditoriaLegada;

        if ($baixaVeiculos->verificaVeiculoProprio()) {
            $baixaVeiculos->setaVariaveis($baixaVeiculos->codigo, $dataBaixa, $motivoBaixa);
            if (!$baixaVeiculos->verificaVeiculoBaixado()) {
                if ($baixaVeiculos->incluiBaixa()) {
                   $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $baixaVeiculos->codigo);
                   $audicao->insereAuditoria();
                } else {
                   alertaAviso($PHP_SELF,"Erro ao baixar o bem no modulo frota","unica","aviso");
                   die;
                }
            }
        }

        $dtAquisicao = pegaDado( 'dt_aquisicao', "patrimonio.bem","Where cod_bem = '".$codBem."' ");
        if ( str_replace('-','',$dtAquisicao) > implode(array_reverse(explode('/',$dataBaixa))) ) {
           sistemaLegado::exibeAviso("A data da baixa deve ser maior que a data de aquisição!","n_incluir","erro");
            die;
        }

        if ( sistemaLegado::comparaDatas( $dataBaixa,date("d/m/Y") ) ) {
            sistemaLegado::exibeAviso("A data da baixa deve ser menor ou igual ao dia de hoje!","n_incluir","erro");
            die;
        }

        //Verifica se o bem já não encontra-se baixado
        if (!pegaDado("cod_bem","patrimonio.bem_baixado","Where cod_bem = '".$codBem."' ")) {

            //Verifica se o código do bem existe
            if (pegaDado("cod_bem","patrimonio.bem","Where cod_bem = '".$codBem."' ")) {
                $bens = new bens;

                if ($bens->baixarBem($codBem,$dataBaixa,$motivoBaixa)) {
                    //Insere auditoria
                        $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codBem);
                        $audicao->insereAuditoria();
                    //Exibe mensagem e redireciona

                    alertaAviso($PHP_SELF, "Bem baixado com sucesso!(Bem:".$codBem.' - '.  pegaDado("descricao","patrimonio.bem","Where cod_bem = '".$codBem."' ") . ")", unica, aviso, Sessao::getId());
                    //alertaAviso($PHP_SELF,"O bem $codBem foi baixado com sucesso","unica","aviso");
                } else {
                    alertaAviso($PHP_SELF,"Não foi possível baixar o bem","unica","erro", Sessao::getId());
                }

            } else {
                alertaAviso($PHP_SELF,"O bem $codBem não existe","unica","erro", Sessao::getId());
            }
        } else {
            alertaAviso($PHP_SELF,"Este bem já está baixado","unica","erro", Sessao::getId());
        }
    break;

    //Baixa em lote
    case 2:
        $bens = new bens;
            if ($bens->baixarBemLote($codInicial,$codFinal,$dataBaixa,$motivoBaixa)) {
                //Insere auditoria
                $audicao = new auditoriaLegada;
                for ($i=$codInicial; $i<=$codFinal; $i++) {
                    $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $i);
                    $audicao->insereAuditoria();
                }
                alertaAviso($PHP_SELF,"Bens baixados com sucesso","unica","aviso", Sessao::getId());
            } else {
                alertaAviso($PHP_SELF,"Não foi possível dar baixa nos bens Por favor consulte o Administrador do Sistema","unica","erro", Sessao::getId());
            }
    break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php'; //Insere o fim da página html

?>
