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
 * Mapeamento da tabela orcamento.pao_ppa_acao
 *
 * @category    Urbem
 * @package     Orcamento
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TOrcamentoPAOPPAAcao extends Persistente
{
    /**
     * Método Construtor da classe TOrcamentoPAOPPAAcao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('orcamento.pao_ppa_acao');
        $this->setCampoCod        ('num_pao');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('exercicio'  ,'varchar', true, '4', true, false);
        $this->AddCampo('num_pao'    ,'integer', true, '' , true, false);
        $this->AddCampo('cod_acao'   ,'integer', true, '' , true, true );
    }

    public function recuperaDadosPao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT LPAD(num_pao::VARCHAR,4,'0') AS cod_acao
                 , nom_pao AS titulo
                 , detalhamento
                 , (SELECT orcamento.fn_consulta_tipo_pao(pao.exercicio,pao.num_pao)) AS cod_tipo
              FROM orcamento.pao
             WHERE num_pao   = " . $this->getDado('num_pao') . "
               AND exercicio = '" . $this->getDado('exercicio') . "'
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function exclusaoPorCodAcao($boTransacao)
    {
        $stSql = "
            SELECT num_pao
                 , cod_acao
                 , exercicio
              FROM orcamento.pao_ppa_acao
             WHERE cod_acao   = " . $this->getDado('cod_acao') . "
               AND exercicio = '" . $this->getDado('exercicio') . "'
        ";

        $obErro = $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);

        if ($rsRecordSet->getNumLinhas() > 0) {
            $this->setDado('num_pao'  , $rsRecordSet->getCampo('num_pao'));
            $this->setDado('cod_acao' , $rsRecordSet->getCampo('cod_acao'));
            $this->setDado('exercicio', $rsRecordSet->getCampo('exercicio'));

            $obErro = $this->exclusao($boTransacao);
        }

        return $obErro;
    }

}
