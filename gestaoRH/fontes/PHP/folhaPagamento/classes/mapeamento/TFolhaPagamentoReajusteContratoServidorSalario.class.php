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
    * Classe de mapeamento da tabela folhapagamento.reajuste_contrato_servidor_salario
    * Data de Criação: 04/12/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoReajusteContratoServidorSalario extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TFolhaPagamentoReajusteContratoServidorSalario()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.reajuste_contrato_servidor_salario");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_reajuste,cod_contrato,timestamp');

        $this->AddCampo('cod_reajuste','integer'  ,true  ,'',true,'TFolhaPagamentoReajuste');
        $this->AddCampo('cod_contrato','integer'  ,true  ,'',true,'TPessoalContratoServidorSalario');
        $this->AddCampo('timestamp'   ,'timestamp',true  ,'',true,'TPessoalContratoServidorSalario');

    }

    public function montaRecuperaReajuste()
    {
        $stSql .= "    SELECT *                                                                                          \n";
        $stSql .= "      FROM folhapagamento.reajuste_contrato_servidor_salario                                 \n";

        if (trim($this->getDado("padrao")) != "") {
            $stSql .= "     WHERE EXISTS ( SELECT *                                                                                                          \n";
            $stSql .= "                      FROM pessoal.contrato_servidor_padrao                                                  \n";
            $stSql .= "                INNER JOIN ( SELECT cod_contrato                                                                                      \n";
            $stSql .= "                                  , max(timestamp) as timestamp                                                                       \n";
            $stSql .= "                               FROM pessoal.contrato_servidor_padrao                                         \n";
            $stSql .= "                           GROUP BY cod_contrato ) as max_contrato_servidor_padrao                                                    \n";
            $stSql .= "                        ON contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato                          \n";
            $stSql .= "                       AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp                                \n";
            $stSql .= "                       AND contrato_servidor_padrao.cod_contrato = reajuste_contrato_servidor_salario.cod_contrato                    \n";
            $stSql .= "                       AND contrato_servidor_padrao.cod_padrao = ".$this->getDado("padrao").")                                        \n";
        }

       return $stSql;
    }

    public function recuperaReajuste(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaReajuste",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaUltimoReajuste()
    {
        $stSql .= "    SELECT *                                                                                          \n";
        $stSql .= "      FROM pessoal.contrato_servidor_salario                                 \n";
        $stSql .= "INNER JOIN (  SELECT cod_contrato                                                                     \n";
        $stSql .= "                   , max(timestamp) as timestamp                                                      \n";
        $stSql .= "                   , max(cod_reajuste) as cod_reajuste                                                \n";
        $stSql .= "               FROM folhapagamento.reajuste_contrato_servidor_salario        \n";
        $stSql .= "           GROUP BY cod_contrato) as reajuste_contrato_servidor_salario                               \n";
        $stSql .= "        ON contrato_servidor_salario.cod_contrato = reajuste_contrato_servidor_salario.cod_contrato   \n";
        $stSql .= "       AND contrato_servidor_salario.timestamp = reajuste_contrato_servidor_salario.timestamp         \n";

       return $stSql;
   }

    public function recuperaUltimoReajuste(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaUltimoReajuste",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

}
?>
