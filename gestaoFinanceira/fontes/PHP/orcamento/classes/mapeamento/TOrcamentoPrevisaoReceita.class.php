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
    * Classe de mapeamento da tabela ORCAMENTO_PREVISAO_RECEITA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.8  2006/08/30 08:52:36  rodrigo
*** empty log message ***

Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoPrevisaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoPrevisaoReceita()
{
    parent::Persistente();
    $this->setTabela('orcamento.previsao_receita');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_receita,periodo');

    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('cod_receita','integer',true,'',true,true);
    $this->AddCampo('periodo','integer',true,'',true,false);
    $this->AddCampo('vl_periodo','numeric',true,'14,02',false,false);

}

function recuperaLimpaReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaLimpaTabelaReceita().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaLimpaTabelaReceita()
{
    $stQuebra = "\n";
    $stSql .= " DELETE FROM ".$this->getTabela()."                      ".$stQuebra;
    $stSql .= " WHERE exercicio   = '". $this->getDado('exercicio')."'    ".$stQuebra;
    $stSql .= " AND   cod_receita = ". $this->getDado('cod_receita')."  ".$stQuebra;

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSql .= "  SELECT                                                         ".$stQuebra;
    $stSql .= "      CR.mascara_classificacao,                                  ".$stQuebra;
    $stSql .= "      CR.descricao,                                              ".$stQuebra;
    $stSql .= "      O.*,                                                       ".$stQuebra;
    $stSql .= "      UE.*                                                       ".$stQuebra;
    $stSql .= "  FROM                                                           ".$stQuebra;
    $stSql .= "      orcamento.vw_classificacao_receita       AS CR,                  ".$stQuebra;
    $stSql .= "      orcamento.receita              AS O,                   ".$stQuebra;
    $stSql .= "      orcamento.usuario_entidade     AS UE                   ".$stQuebra;
    $stSql .= "  WHERE                                                          ".$stQuebra;
    $stSql .= "          CR.exercicio IS NOT NULL                               ".$stQuebra;
    $stSql .= "      AND O.cod_conta     = CR.cod_conta                         ".$stQuebra;
    $stSql .= "      AND O.exercicio     = CR.exercicio                         ".$stQuebra;
    $stSql .= "      AND UE.exercicio    = O.exercicio                          ".$stQuebra;
    $stSql .= "      AND UE.cod_entidade = O.cod_entidade                       ".$stQuebra;

    return $stSql;
}

/**
    * Executa montaRecuperaEstrutural no banco
    * @access private
    * @param Object $rsRecordSet
    * @param String $stFiltro
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function recuperaEstrutural(&$rsRecordSet, $stFiltro, $stOrder, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaEstrutural().$stFiltro.$stOrdem;
//  $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEstrutural()
{
    $stSql  = "SELECT OCR.cod_estrutural                  \n";
    $stSql .= "FROM  orcamento.receita       AS ORE   \n";
    $stSql .= "     ,orcamento.conta_receita AS OCR   \n";
    $stSql .= "WHERE ORE.exercicio = OCR.exercicio        \n";
    $stSql .= "AND   ORE.cod_conta = OCR.cod_conta        \n";

    return $stSql;
}

    public function recuperaDadosExportacaoMeta(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaDadosExportacaoMeta($stFiltro, $stOrdem);
        $this->setDebug($stSQL);

        return $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);
    }

    private function montaRecuperaDadosExportacaoMeta($stFiltro = '', $stOrdem = '')
    {
        $stSql = "
            SELECT REPLACE((SELECT COALESCE(SUM(vl_periodo), 0.00)
                      FROM orcamento.previsao_receita
                      JOIN orcamento.receita
                        ON receita.cod_receita = previsao_receita.cod_receita
                       AND receita.exercicio = previsao_receita.exercicio
                     WHERE previsao_receita.periodo = 1
                       AND receita.cod_entidade IN (".$this->getDado('entidades').")
                       AND previsao_receita.exercicio = '".Sessao::getExercicio()."')::VARCHAR,'.',',') AS meta_arrec_bim_1
                 , REPLACE((SELECT COALESCE(SUM(vl_periodo), 0.00)
                      FROM orcamento.previsao_receita
                      JOIN orcamento.receita
                        ON receita.cod_receita = previsao_receita.cod_receita
                       AND receita.exercicio = previsao_receita.exercicio
                     WHERE previsao_receita.periodo = 2
                       AND receita.cod_entidade IN (".$this->getDado('entidades').")
                       AND previsao_receita.exercicio = '".Sessao::getExercicio()."')::VARCHAR,'.',',') AS meta_arrec_bim_2
                 , REPLACE((SELECT COALESCE(SUM(vl_periodo), 0.00)
                      FROM orcamento.previsao_receita
                      JOIN orcamento.receita
                        ON receita.cod_receita = previsao_receita.cod_receita
                       AND receita.exercicio = previsao_receita.exercicio
                     WHERE previsao_receita.periodo = 3
                       AND receita.cod_entidade IN (".$this->getDado('entidades').")
                       AND previsao_receita.exercicio = '".Sessao::getExercicio()."')::VARCHAR,'.',',') AS meta_arrec_bim_3
                 , REPLACE((SELECT COALESCE(SUM(vl_periodo), 0.00)
                      FROM orcamento.previsao_receita
                      JOIN orcamento.receita
                        ON receita.cod_receita = previsao_receita.cod_receita
                       AND receita.exercicio = previsao_receita.exercicio
                     WHERE previsao_receita.periodo = 4
                       AND receita.cod_entidade IN (".$this->getDado('entidades').")
                       AND previsao_receita.exercicio = '".Sessao::getExercicio()."')::VARCHAR,'.',',') AS meta_arrec_bim_4
                 , REPLACE((SELECT COALESCE(SUM(vl_periodo), 0.00)
                      FROM orcamento.previsao_receita
                      JOIN orcamento.receita
                        ON receita.cod_receita = previsao_receita.cod_receita
                       AND receita.exercicio = previsao_receita.exercicio
                     WHERE previsao_receita.periodo = 5
                       AND receita.cod_entidade IN (".$this->getDado('entidades').")
                       AND previsao_receita.exercicio = '".Sessao::getExercicio()."')::VARCHAR,'.',',') AS meta_arrec_bim_5
                 , REPLACE((SELECT COALESCE(SUM(vl_periodo), 0.00)
                      FROM orcamento.previsao_receita
                      JOIN orcamento.receita
                        ON receita.cod_receita = previsao_receita.cod_receita
                       AND receita.exercicio = previsao_receita.exercicio
                     WHERE previsao_receita.periodo = 6
                       AND receita.cod_entidade IN (".$this->getDado('entidades').")
                       AND previsao_receita.exercicio = '".Sessao::getExercicio()."')::VARCHAR,'.',',') AS meta_arrec_bim_6
         ";

        return $stSql;
    }
}
