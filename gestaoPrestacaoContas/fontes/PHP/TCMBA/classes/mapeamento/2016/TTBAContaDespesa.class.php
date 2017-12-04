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
    * Extensão da Classe de mapeamento
    * Data de Criação: 14/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63115 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php" );

class TTBAContaDespesa extends TOrcamentoContaDespesa
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTBAContaDespesa()
    {
        parent::TOrcamentoContaDespesa();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaDadosTribunal()
    {
        $stSql = "
                    SELECT 1 AS tipo_registro
                         , ".$this->getDado('inCodGestora')." AS unidade_gestora
                         , REPLACE( conta_despesa.cod_estrutural, '.', '') AS item_despesa
                         , REPLACE( conta_despesa.cod_estrutural, '.', '') AS item_despesa_tcm
                         , conta_despesa.descricao
                         , conta_despesa.exercicio
                         , CASE WHEN orcamento.fn_tipo_conta_despesa(exercicio, cod_estrutural) = 'A' 
                                THEN 1 
                                ELSE 2 
                           END AS recebe_lancamento 
            
                     FROM orcamento.conta_despesa
                 
                    WHERE conta_despesa.exercicio = '".$this->getDado('stExercicio')."' 
                 ORDER BY conta_despesa.exercicio
                        , conta_despesa.cod_estrutural 
        ";
        return $stSql;
    }

}

?>