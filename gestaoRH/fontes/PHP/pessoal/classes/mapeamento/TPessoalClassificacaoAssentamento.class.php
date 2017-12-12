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
* Classe de mapeamento da tabela PESSOAL_CLASSIFICACAO_ASSENTAMENTO
* Data de Criação   : 10/03/2004

* @author Analista: ???
* @author Programador: Marcelo Boezzio Paulino

* @package URBEM
* @subpackage Mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-10-25 16:30:17 -0200 (Qui, 25 Out 2007) $

Caso de uso: uc-04.04.08
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPessoalClassificacaoAssentamento extends Persistente
{

    public function TPessoalClassificacaoAssentamento()
    {
        parent::Persistente();
        $this->setTabela('pessoal.classificacao_assentamento');
        $this->setCampoCod('cod_classificacao');

        $this->AddCampo('cod_classificacao' ,'integer',true,'',true,false);
        $this->AddCampo('descricao'         ,'varchar',true,'',false,false);
        $this->AddCampo('cod_tipo'          ,'integer',true,'',false,true);
    }

    public function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = $stOrdem ? $stOrdem : " ORDER BY ca.descricao ";
        $stSql  = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamento()
    {
       $stSQL  = "SELECT                                                         \n";
       $stSQL .= "   ca.*,                                                       \n";
       $stSQL .= "   trim(tc.descricao) as descricao_tipo                        \n";
       $stSQL .= "FROM                                                           \n";
       $stSQL .= "   pessoal.classificacao_assentamento as ca,               \n";
       $stSQL .= "   pessoal.tipo_classificacao as tc                        \n";
       $stSQL .= " WHERE                                                         \n";
       $stSQL .= "   ca.cod_tipo = tc.cod_tipo                                   \n";

       return $stSQL;
    }

    public function recuperaPorContrato(&$rsRecordSet, $comboType, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = "GROUP BY ca.cod_classificacao
                        , ca.cod_tipo
                        , ca.descricao
                        , tc.descricao";
        $stOrdem = $stOrdem ? $stOrdem : " ORDER BY ca.descricao ";
        $stFiltro = $stFiltro ? " WHERE ".substr($stFiltro, 4) : "";
        $stSql  = $this->montaRecuperaPorContrato($comboType).$stFiltro.$stGroup.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPorContrato($comboType)
    {
        $stSql = "
            SELECT
               ca.*,
               trim(tc.descricao) as descricao_tipo
            FROM
               pessoal.classificacao_assentamento as ca
            INNER JOIN
               pessoal.tipo_classificacao as tc
            ON
               ca.cod_tipo = tc.cod_tipo
            INNER JOIN
               pessoal.assentamento_assentamento
            ON
               ca.cod_classificacao = assentamento_assentamento.cod_classificacao
            INNER JOIN
               pessoal.assentamento
            ON
               assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento AND
               assentamento.timestamp = (SELECT MAX(timestamp)
                                           FROM pessoal.assentamento sdf
                                          WHERE assentamento.cod_assentamento = sdf.cod_assentamento )
            INNER JOIN
               pessoal.assentamento_sub_divisao
            ON
               assentamento_sub_divisao.cod_assentamento = assentamento.cod_assentamento AND
               assentamento_sub_divisao.timestamp = assentamento.timestamp AND
               assentamento_sub_divisao.timestamp = (SELECT MAX(timestamp)
                                                           FROM pessoal.assentamento_sub_divisao sdf
                                                          WHERE assentamento_sub_divisao.cod_assentamento = sdf.cod_assentamento
                                                            AND assentamento_sub_divisao.cod_sub_divisao = sdf.cod_sub_divisao )

            INNER JOIN
               pessoal.contrato_servidor_sub_divisao_funcao
            ON
               contrato_servidor_sub_divisao_funcao.cod_sub_divisao = assentamento_sub_divisao.cod_sub_divisao AND
               contrato_servidor_sub_divisao_funcao.timestamp = (SELECT MAX(timestamp)
                                                           FROM pessoal.contrato_servidor_sub_divisao_funcao sdf
                                                          WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = sdf.cod_contrato
                                                            AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = sdf.cod_sub_divisao )
            INNER JOIN
               pessoal.contrato
            ON
               contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
             ";
            switch ($comboType) {
                case 'cargo':
                    $stSql .= " INNER JOIN
                                   pessoal.contrato_servidor
                                ON
                                   contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor.cod_contrato ";
                    break;

                case 'lotacao':
                    $stSql .= " INNER JOIN
                                   pessoal.contrato_servidor
                                ON
                                   contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor.cod_contrato
                                INNER JOIN
                                   pessoal.contrato_servidor_orgao
                                ON
                                   contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato AND
                                   contrato_servidor_orgao.timestamp = (SELECT MAX(timestamp)
                                                                          FROM pessoal.contrato_servidor_orgao orgao
                                                                         WHERE contrato_servidor_orgao.cod_contrato = orgao.cod_contrato
                                                                           AND contrato_servidor_orgao.cod_orgao = orgao.cod_orgao ) ";
                    break;
            }

        return $stSql;
    }

    public function validaAlteracao($stFiltro = "", $boTransacao = "")
    {
        $obErro = new erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaValidaAlteracao().$stFiltro;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $rsRecordSet->getNumLinhas() > 0 ) {
                $obErro->setDescricao('Já existe uma classificação com essa descrição!');
            }
        }

        return $obErro;
    }

    public function montaValidaAlteracao()
    {
        $stSQL  = "SELECT                                                               \n";
        $stSQL .= "    *                                                                \n";
        $stSQL .= "FROM                                                                 \n";
        $stSQL .= "    pessoal.classificacao_assentamento                           \n";
        $stSQL .= "WHERE cod_classificacao <>   ".$this->getDado('cod_classificacao')." \n";
        $stSQL .= "AND   descricao         like '".$this->getDado('descricao')."'       \n";

        return $stSQL;
    }

    public function recuperaClassificacaoAssentamentoLicencaPremio(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaClassificacaoAssentamentoLicencaPremio",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaClassificacaoAssentamentoLicencaPremio()
    {
        $stSql  = "SELECT classificacao_assentamento.*                                                              \n";
        $stSql .= "  FROM pessoal.classificacao_assentamento                              \n";
        $stSql .= " WHERE cod_tipo = 2                                                                              \n";
        $stSql .= "   AND EXISTS (SELECT 1                                                                          \n";
        $stSql .= "                 FROM pessoal.assentamento_assentamento                \n";
        $stSql .= "                WHERE cod_classificacao = classificacao_assentamento.cod_classificacao           \n";
        $stSql .= "                  AND cod_motivo = 9)                                                            \n";

        return $stSql;
    }

}

?>
