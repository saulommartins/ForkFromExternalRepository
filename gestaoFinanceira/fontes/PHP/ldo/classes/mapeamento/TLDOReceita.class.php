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
 * Pagina de MAPEAMENTO Receita tipo do uc-02.10.04
 * Data de Criação: 05/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
 */

//include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php');

class TLDOReceita extends Persistente
{

    /**
     * Método Construtor
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ldo.receita');
        $this->setCampoCod('cod_receita');

        // campo, tipo, not_null, data_length, pk, fk
        $this->AddCampo('cod_receita','integer',true,'',true,false);
        $this->AddCampo('ano','integer',true,'',false,false);
        $this->AddCampo('cod_receita_ppa','integer',true,'',true,false);
        $this->AddCampo('cod_entidade','integer',true,'',true,false);
        $this->AddCampo('cod_conta','integer',true,'',true,false);
        $this->AddCampo('exercicio','integer',true,'',true,false);
        $this->AddCampo('cod_ppa','integer',true,'',true,false);
        $this->AddCampo('valor_total','numeric',true,'14,2',false,false);
        $this->AddCampo('ativo','char',true,'',false,false);
    }

    public function recuperaDadosReceita(&$rsRecordSet, $stCriterio, $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql       = $this->montaRecuperaDadosReceita($stCriterio).$stOrdem;
        $obErro      = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDadosReceita($stCriterio)
    {
        $stSQL = "   SELECT DISTINCT ON (receita.cod_receita_ppa)                            \n";
        $stSQL .= "                       receita.cod_receita                                \n";
        $stSQL .= "                     , receita.ano                                        \n";
        $stSQL .= "                     , ocr.cod_estrutural                                 \n";
        $stSQL .= "                     , receita.cod_conta                                  \n";
        $stSQL .= "                     , receita.cod_receita_ppa                            \n";
        $stSQL .= "                     , receita.ativo                                      \n";
        $stSQL .= "                     , receita_dados.cod_receita_dados                    \n";
        $stSQL .= "                     , ocr.descricao                                      \n";
        $stSQL .= "                     , to_real(receita.valor_total) as total_receita      \n";
        $stSQL .= "                     , pr.valor_total as total_receita_ppa                \n";
        $stSQL .= "                     , pr.cod_ppa                                         \n";
        $stSQL .= "                     , receita.valor_total   as total_lancado             \n";
        $stSQL .= "                  FROM ldo.receita                                        \n";
        $stSQL .= "            INNER JOIN ldo.receita_dados                                  \n";
        $stSQL .= "                    ON receita.cod_receita = receita_dados.cod_receita    \n";
        $stSQL .= "            INNER JOIN ppa.ppa_receita as pr                              \n";
        $stSQL .= "                    ON pr.cod_receita = receita.cod_receita_ppa           \n";
        $stSQL .= "            INNER JOIN orcamento.conta_receita ocr                        \n";
        $stSQL .= "                    ON (ocr.cod_conta = receita.cod_conta                 \n";
        $stSQL .= "                   AND ocr.exercicio = receita.exercicio)                 \n";
        $stSQL .= $stCriterio;
        $stSQL .= "              ORDER BY receita.cod_receita_ppa ASC                        \n";
        $stSQL .= "                     , receita_dados.cod_receita_dados DESC               \n";

        return $stSQL;
    }

    public function recuperaTotalReceitaLDO(&$rsRecordSet, $stCriterio, $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql       = $this->montaRecuperaTotalReceitaLDO($stCriterio).$stOrdem;
        $obErro      = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaTotalReceitaLDO($stCriterio)
    {
        $stSQL = "   SELECT sum(receita.valor_total) as total FROM ldo.receita              \n";
        $stSQL .= $stCriterio;

        return $stSQL;
    }

}
