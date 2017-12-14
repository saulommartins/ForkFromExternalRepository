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
  * Mapeamento da tabela frota.veiculo
  * Data de criação : 13/03/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 27758 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-28 07:15:55 -0200 (Seg, 28 Jan 2008) $

    Caso de uso: uc-03.02.10
**/

/*
$Log$
Revision 1.7  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:11:17  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaManutencao extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaManutencao()
    {
        parent::Persistente();
        $this->setTabela('frota.manutencao');
        $this->setCampoCod('cod_manutencao');
        $this->setComplementoChave('exercicio');
        $this->AddCampo('cod_manutencao','integer',true,'',true,false);
        $this->AddCampo('exercicio','varchar',true,'"4"',true,false);
        $this->AddCampo('cod_veiculo','integer',true,'',false,true);
        $this->AddCampo('dt_manutencao','date',true,'',false,false);
        $this->AddCampo('km','numeric',true,"14.2",false,false);
        $this->AddCampo('observacao', 'text',false,'',false,false);
    }

    public function recuperaManutencaoSintetico(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaManutencaoSintetico().$stFiltro.$stOrder;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaRecuperaManutencaoSintetico()
    {
        $stSql = " SELECT manutencao.cod_manutencao
                        , manutencao.exercicio
                        , TO_CHAR( manutencao.dt_manutencao, 'dd/mm/yyyy' ) AS dt_manutencao
                        , manutencao.observacao
                        , veiculo.cod_veiculo
                        , SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) AS placa_masc
                        , marca.nom_marca
                        , modelo.nom_modelo
                     FROM frota.manutencao
               INNER JOIN frota.veiculo
                       ON veiculo.cod_veiculo = manutencao.cod_veiculo
               INNER JOIN frota.marca
                       ON marca.cod_marca = veiculo.cod_marca
               INNER JOIN frota.modelo
                       ON modelo.cod_marca = veiculo.cod_marca
                      AND modelo.cod_modelo = veiculo.cod_modelo
            ";

       return $stSql;
    }

    public function recuperaManutencaoAnalitica(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaManutencaoAnalitica",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaManutencaoAnalitica()
    {
        $stSql = " SELECT manutencao.cod_manutencao
                        , manutencao.exercicio
                        , TO_CHAR( manutencao.dt_manutencao, 'dd/mm/yyyy' ) AS dt_manutencao
                        , manutencao.cod_veiculo
                        , manutencao.km
                        , manutencao.observacao
                        , manutencao_empenho.cod_empenho
                        , efetivacao.cod_autorizacao
                        , efetivacao.exercicio_autorizacao
                        , empenho_beneficiario.nom_cgm AS nom_empenho
                        , manutencao_empenho.cod_entidade
                        , entidade_cgm.nom_cgm AS nom_entidade
                        , manutencao_empenho.exercicio_empenho
                        , SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) AS placa_masc
                        , veiculo.prefixo
                        , modelo.nom_modelo
                     FROM frota.manutencao
                LEFT JOIN frota.efetivacao
                       ON efetivacao.cod_manutencao = manutencao.cod_manutencao
                      AND efetivacao.exercicio_manutencao = manutencao.exercicio
                LEFT JOIN frota.veiculo
                       ON veiculo.cod_veiculo = manutencao.cod_veiculo
                LEFT JOIN frota.modelo
                       ON modelo.cod_marca = veiculo.cod_marca
                      AND modelo.cod_modelo = veiculo.cod_modelo
                LEFT JOIN frota.manutencao_empenho
                       ON manutencao_empenho.exercicio = manutencao.exercicio
                      AND manutencao_empenho.cod_manutencao = manutencao.cod_manutencao
                LEFT JOIN empenho.empenho
                       ON empenho.cod_empenho = manutencao_empenho.cod_empenho
                      AND empenho.exercicio = manutencao_empenho.exercicio_empenho
                      AND empenho.cod_entidade = manutencao_empenho.cod_entidade
                LEFT JOIN empenho.pre_empenho
                       ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                      AND pre_empenho.exercicio = empenho.exercicio
                LEFT JOIN sw_cgm AS empenho_beneficiario
                       ON empenho_beneficiario.numcgm = pre_empenho.cgm_beneficiario
                LEFT JOIN orcamento.entidade
                       ON entidade.cod_entidade = manutencao_empenho.cod_entidade
                      AND entidade.exercicio = manutencao_empenho.exercicio_empenho
                LEFT JOIN sw_cgm AS entidade_cgm
                       ON entidade_cgm.numcgm = entidade.numcgm
                    WHERE ";
        if ( $this->getDado( 'cod_manutencao' ) != '' ) {
            $stSql .= " manutencao.cod_manutencao = ".$this->getDado('cod_manutencao')." AND   ";
        }
        if ( $this->getDado( 'exercicio' ) != '' ) {
            $stSql .= " manutencao.exercicio = '".$this->getDado('exercicio')."' AND   ";
        }

        return substr($stSql,0,-6);
    }
}
