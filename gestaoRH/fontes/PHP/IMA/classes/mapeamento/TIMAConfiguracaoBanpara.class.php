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
    * Classe de mapeamento da tabela ima.configuracao_banpara
    * Data de Criação: 01/09/2009

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-tabelas

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.configuracao_banpara
  * Data de Criação: 01/09/2009

  * @author Desenvolvedor: Rafael Garbin

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConfiguracaoBanpara extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TIMAConfiguracaoBanpara()
    {
        parent::Persistente();
        $this->setTabela("ima.configuracao_banpara");

        $this->setCampoCod('num_orgao_banpara');
        $this->setComplementoChave('cod_empresa,timestamp');

        $this->AddCampo('cod_empresa'      , 'integer'      , true, ''  , true , "TIMAConfiguracaoBanparaEmpresa");
        $this->AddCampo('num_orgao_banpara', 'sequence'     , true, ''  , true , false);
        $this->AddCampo('timestamp'        , 'timestamp_now', true, ''  , true , false);
        $this->AddCampo('descricao'        , 'varchar'      , true, '40', false, false);
        $this->AddCampo('vigencia'         , 'date'         , true, ''  , false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "    SELECT *                                                          				 						\n";
        $stSql .= "      FROM ima.configuracao_banpara                                                        				 	\n";
        $stSql .= "INNER JOIN ( SELECT configuracao_banpara.cod_empresa									   					 	\n";
        $stSql .= "		   			 , max(configuracao_banpara.timestamp) as timestamp                       				 	\n";
        $stSql .= "		  	      FROM ima.configuracao_banpara                                               				 	\n";
        $stSql .= "		  		 WHERE configuracao_banpara.vigencia = to_date('".$this->getDado("vigencia")."','dd/mm/yyyy') 	\n";
        $stSql .= "			  GROUP BY configuracao_banpara.cod_empresa									   					 	\n";
        $stSql .= "			  ) as max_configuracao_banpara	                                                   				 	\n";
        $stSql .= "		   ON configuracao_banpara.cod_empresa = max_configuracao_banpara.cod_empresa      			 			\n";
        $stSql .= "		  AND configuracao_banpara.timestamp = max_configuracao_banpara.timestamp 		   			 			\n";
        $stSql .= "INNER JOIN ima.configuracao_banpara_empresa																	\n";
        $stSql .= "        ON configuracao_banpara.cod_empresa = configuracao_banpara_empresa.cod_empresa						\n";

        if (trim($this->getDado("cod_empresa")) != "") {
            $stSql .= "	WHERE configuracao_banpara.cod_empresa = ".$this->getDado("cod_empresa");
        }

        return $stSql;
    }

    public function recuperaVigencias(&$rsRecordSet, $stFiltro="", $stOrdem="")
    {
        $obErro = $this->executaRecupera("montaRecuperaVigencias",$rsRecordSet,$stFiltro,$stOrdem);

        return $obErro;
    }

    public function montaRecuperaVigencias()
    {
        $stSql  = "	   SELECT ultima_vigencia_competencia.vigencia as dt_vigencia					    \n";
        $stSql .= "         , to_char(ultima_vigencia_competencia.vigencia,'dd/mm/yyyy') as vigencia	\n";
        $stSql .= "         , ultima_vigencia_competencia.cod_periodo_movimentacao 					\n";
        $stSql .= "      FROM (   SELECT DISTINCT max(vigencia) as vigencia								\n";
        $stSql .= "                    , ( SELECT cod_periodo_movimentacao 								\n";
        $stSql .= "                          FROM folhapagamento.periodo_movimentacao					\n";
        $stSql .= "                         WHERE vigencia BETWEEN dt_inicial AND dt_final				\n";
        $stSql .= "                       ) as cod_periodo_movimentacao 								\n";
        $stSql .= "                 FROM ima.configuracao_banpara 										\n";
        $stSql .= "             GROUP BY cod_periodo_movimentacao 										\n";
        $stSql .= "         ) as ultima_vigencia_competencia											\n";

        return $stSql;
    }
}
?>
