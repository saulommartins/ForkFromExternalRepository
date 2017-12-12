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
    * Classe de mapeamento da tabela ima.conferencia_910
    * Data de Criação: 03/06/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: TIMAConferencia910.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.conferencia_910
  * Data de Criação: 03/06/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConferencia910 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConferencia910()
{
    parent::Persistente();
    $this->setTabela("ima.conferencia_910");

    $this->setCampoCod('cod_conferencia');
    $this->setComplementoChave('');

    $this->AddCampo('cod_conferencia','sequence',true  ,''      ,true,false);
    $this->AddCampo('cod_contrato'   ,'integer' ,true  ,''      ,false,'TPessoalContrato');
    $this->AddCampo('valor_pasep'    ,'numeric' ,true  ,'15,2'  ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "     SELECT contrato.*                                                                                                			\n";
    $stSql .= "          , (SELECT translate(servidor_pis_pasep,'.-','') FROM sw_cgm_pessoa_fisica WHERE numcgm = servidor.numcgm) as pis_pasep	\n";
    $stSql .= "          , to_real(conferencia_910.valor_pasep) as valor                                                             			\n";
    $stSql .= "       FROM pessoal.contrato                                                                										\n";
    $stSql .= " INNER JOIN pessoal.servidor_contrato_servidor                                              										\n";
    $stSql .= "  		ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato														\n";
    $stSql .= " INNER JOIN pessoal.servidor                                                               										\n";
    $stSql .= " 	    ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor														\n";
    $stSql .= " INNER JOIN ima.conferencia_910                                                             									    \n";
    $stSql .= "		    ON contrato.cod_contrato = conferencia_910.cod_contrato                                                      			\n";

    // Folha Salário
    if ((int) $this->getDado("inFolhaPagamento") == 1) {
        $stSql .= "   AND NOT EXISTS (SELECT 1                                                                                      			\n";
        $stSql .= "                     FROM folhapagamento.registro_evento_periodo                                                 			\n";
        $stSql .= "                        , folhapagamento.evento_calculado                                                        			\n";
        $stSql .= "                    WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro                   			\n";
        $stSql .= "                      AND evento_calculado.cod_evento = (SELECT cod_evento FROM ima.configuracao_pasep)						\n";
        $stSql .= "                      AND registro_evento_periodo.cod_periodo_movimentacao = (SELECT cod_periodo_movimentacao    			\n";
        $stSql .= "                                                                                FROM folhapagamento.periodo_movimentacao		\n";
        $stSql .= "                                                                            ORDER BY cod_periodo_movimentacao DESC limit 1)	\n";
        $stSql .= "                      AND registro_evento_periodo.cod_contrato = contrato.cod_contrato);                         			\n";
    }

    // Folha Complementar
    if ((int) $this->getDado("inFolhaPagamento") == 3) {
        $stSql .= "     WHERE NOT EXISTS (SELECT 1 																											\n";
        $stSql .= "                         FROM folhapagamento.registro_evento_complementar 																\n";
        $stSql .= "                            , folhapagamento.evento_complementar_calculado 																\n";
        $stSql .= "                        WHERE registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro 				\n";
        $stSql .= "                          AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento 					\n";
        $stSql .= "                          AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao 			\n";
        $stSql .= "                          AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro  			\n";
        $stSql .= "                          AND evento_complementar_calculado.cod_evento      = (SELECT cod_evento FROM ima.configuracao_pasep) 			\n";
        $stSql .= "                          AND registro_evento_complementar.cod_periodo_movimentacao = (SELECT cod_periodo_movimentacao 					\n";
        $stSql .= "                                                                                         FROM folhapagamento.periodo_movimentacao 		\n";
        $stSql .= "                                                                                      ORDER BY cod_periodo_movimentacao DESC limit 1) 	\n";
        $stSql .= "                          AND registro_evento_complementar.cod_contrato = contrato.cod_contrato) ";
    }

    return $stSql;
}

}
?>
