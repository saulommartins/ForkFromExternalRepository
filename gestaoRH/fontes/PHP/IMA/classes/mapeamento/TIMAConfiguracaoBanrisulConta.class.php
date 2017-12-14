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
    * Classe de mapeamento da tabela ima.configuracao_banrisul_conta
    * Data de Criação: 02/04/2009

    * @author Analista     : Dagiane
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TIMAConfiguracaoBanrisulConta extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TIMAConfiguracaoBanrisulConta()
    {
        parent::Persistente();
        $this->setTabela("ima.configuracao_banrisul_conta");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_convenio,cod_banco,cod_agencia,cod_conta_corrente,timestamp');

        $this->AddCampo('cod_convenio'      ,'integer'       ,true  ,''    ,true  ,'TIMAConfiguracaoConvenioBanrisul');
        $this->AddCampo('cod_banco'         ,'integer'       ,true  ,''    ,true  ,'TIMAConfiguracaoConvenioBanrisul');
        $this->AddCampo('cod_agencia'       ,'integer'       ,true  ,''    ,true  ,'TMONContaCorrente');
        $this->AddCampo('cod_conta_corrente','integer'       ,true  ,''    ,true  ,'TMONContaCorrente');
        $this->AddCampo('timestamp'         ,'timestamp_now' ,true  ,''    ,true  ,false);
        $this->AddCampo('descricao'         ,'varchar'		 ,true  ,'60'  ,false ,false);
        $this->AddCampo('vigencia'          ,'date'          ,true  ,''    ,false ,false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "    SELECT configuracao_banrisul_conta.*                                                          				 \n";
        $stSql .= "         , conta_corrente.num_conta_corrente                                                						 \n";
        $stSql .= "         , agencia.num_agencia                                                              						 \n";
        $stSql .= "         , agencia.nom_agencia                                                              						 \n";
        $stSql .= "         , banco.num_banco                                                                  						 \n";
        $stSql .= "         , banco.nom_banco                                                                  						 \n";
        $stSql .= "      FROM ima.configuracao_banrisul_conta                                                        				 \n";
        $stSql .= "INNER JOIN monetario.conta_corrente                                                         						 \n";
        $stSql .= "        ON configuracao_banrisul_conta.cod_conta_corrente = conta_corrente.cod_conta_corrente     				 \n";
        $stSql .= "       AND configuracao_banrisul_conta.cod_agencia = conta_corrente.cod_agencia                   				 \n";
        $stSql .= "       AND configuracao_banrisul_conta.cod_banco   = conta_corrente.cod_banco                     				 \n";
        $stSql .= "INNER JOIN ( SELECT configuracao_banrisul_conta.cod_convenio									   					 \n";
        $stSql .= "		   			 , max(configuracao_banrisul_conta.timestamp) as timestamp                       				 \n";
        $stSql .= "		  	      FROM ima.configuracao_banrisul_conta                                               				 \n";
        $stSql .= "		  		 WHERE configuracao_banrisul_conta.vigencia = to_date('".$this->getDado("vigencia")."','dd/mm/yyyy') \n";
        $stSql .= "			  GROUP BY configuracao_banrisul_conta.cod_convenio                                      				 \n";
        $stSql .= "			  ) as max_configuracao_banrisul_conta                                                   				 \n";
        $stSql .= "		   ON configuracao_banrisul_conta.cod_convenio = max_configuracao_banrisul_conta.cod_convenio      			 \n";
        $stSql .= "		  AND configuracao_banrisul_conta.timestamp = max_configuracao_banrisul_conta.timestamp 		   			 \n";
        $stSql .= "INNER JOIN monetario.agencia                                                                						 \n";
        $stSql .= "        ON conta_corrente.cod_agencia = agencia.cod_agencia                                 						 \n";
        $stSql .= "       AND conta_corrente.cod_banco   = agencia.cod_banco                                   						 \n";
        $stSql .= "INNER JOIN monetario.banco                                                                  						 \n";
        $stSql .= "        ON agencia.cod_banco = banco.cod_banco                                              						 \n";

        return $stSql;
    }

    public function recuperaVigencias(&$rsRecordSet, $stFiltro="", $stOrdem="")
    {
        $obErro = $this->executaRecupera("montaRecuperaVigencias",$rsRecordSet,$stFiltro,$stOrdem);

        return $obErro;
    }

    public function montaRecuperaVigencias()
    {
        $stSql  = "  SELECT ultima_vigencia_competencia.vigencia as dt_vigencia						\n";
        $stSql .= "       , to_char(ultima_vigencia_competencia.vigencia,'dd/mm/yyyy') as vigencia	\n";
        $stSql .= "       , ultima_vigencia_competencia.cod_periodo_movimentacao 					\n";
        $stSql .= "    FROM (   SELECT DISTINCT max(vigencia) as vigencia							\n";
        $stSql .= "                  , ( SELECT cod_periodo_movimentacao 							\n";
        $stSql .= "                        FROM folhapagamento.periodo_movimentacao					\n";
        $stSql .= "                       WHERE vigencia BETWEEN dt_inicial AND dt_final			\n";
        $stSql .= "                    ) as cod_periodo_movimentacao 								\n";
        $stSql .= "               FROM ima.configuracao_banrisul_conta 								\n";
        $stSql .= "           GROUP BY cod_periodo_movimentacao 									\n";
        $stSql .= "         ) as ultima_vigencia_competencia										\n";

        return $stSql;
    }
}
?>
