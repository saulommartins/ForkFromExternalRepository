<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                           *
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
    * Página de Mapeamento para a Exportação Arquivos Orcamento TCM-BA LimiteCred.txt
    * Data de Criação   : 15/09/2015
    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Evandro Melos
    * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBALimiteCred extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct ()
{
    parent::Persistente();
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
    $stSql .= " SELECT   1 AS tipo_registro
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                       , tcmba.limite_alteracao_credito.exercicio AS ano_alteracao
                       , norma.nom_norma AS descricao_lei
                       , norma.num_norma AS numero_lei
                       , norma.dt_publicacao AS data_lei
                       , limite_alteracao_credito.cod_tipo_alteracao AS tipo_alteracao
                       , limite_alteracao_credito.valor_alteracao AS valor_alteracao
                  
                  FROM tcmba.limite_alteracao_credito
            
            INNER JOIN normas.norma
                    ON norma.cod_norma = limite_alteracao_credito.cod_norma

            WHERE limite_alteracao_credito.exercicio    = '".$this->getDado('exercicio')."'
              AND limite_alteracao_credito.cod_entidade = ".$this->getDado('cod_entidade')."
            ";
    return $stSql;
}

}//End of class
