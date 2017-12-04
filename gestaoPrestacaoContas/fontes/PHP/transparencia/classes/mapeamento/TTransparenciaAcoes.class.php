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
 * Classe de mapeamento da tabela ORCAMENTO.PAO
 * Data de Criação: 13/07/2004

 * @author Analista: Jorge B. Ribarr
 * @author Desenvolvedor: Marcelo B. Paulino

 * @package URBEM
 * @subpackage Mapeamento

 $Id:$

 * Casos de uso: uc-02.01.03 , uc-02.08.02
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  ORCAMENTO.PAO
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino
  */

class TTransparenciaAcoes extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTransparenciaAcoes()
    {
        parent::Persistente();
        $this->setTabela('orcamento.pao');

        $this->setCampoCod('num_pao');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('exercicio','char',true,'04',true,false);
        $this->AddCampo('num_pao','integer',true,'',true,false);
        $this->AddCampo('nom_pao','varchar',true,'80',false,false);
        $this->AddCampo('detalhamento','text',true,'',false,false);
    }

    public function recuperaDadosExportacao(&$rsRecordSet, $boTransacao = ""){
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosExportacao();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosExportacao(){
        $stSql = " SELECT 
                            pao.exercicio
                            , CASE WHEN ppa.acao.num_acao IS NOT NULL THEN 
                                    ppa.acao.num_acao
                              ELSE pao.num_pao
                              END AS num_pao
                            , pao.nom_pao
                            , pao.detalhamento 
                    FROM orcamento.pao 
               LEFT JOIN orcamento.pao_ppa_acao
                     ON( pao_ppa_acao.exercicio = pao.exercicio 
                     AND pao_ppa_acao.num_pao   = pao.num_pao )
               LEFT JOIN ppa.acao
                     ON( acao.cod_acao = pao_ppa_acao.cod_acao )
                WHERE pao.exercicio = '".$this->getDado('exercicio')."'
                ORDER BY num_pao 
        ";

        return $stSql;
    }

}
