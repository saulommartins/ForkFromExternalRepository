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
    * Classe de mapeamento da tabela TCMPA.ORGAO_UNIDADE_GESTORA
    * Data de Criação: 19/12/2007

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  TCMPA.TIPO_UNIDADE_GESTORA
  * Data de Criação: 19/12/2007

  * @author Analista: Gelson W. Golçalves
  * @author Desenvolvedor: Henrique Girardi dos Santos

*/
class TTPAUnidadeOrcamentaria extends Persistente
{

    /**
      * Método Construtor
      * @access Private
    */
    public function TTPAUnidadeOrcamentaria()
    {
        parent::Persistente();
        $this->setTabela('tcmpa.unidade_orcamentaria');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_orgao, cod_organograma, cod_nivel, cod_entidade, unidade_gestora');

        $this->AddCampo( 'cod_orgao'      , 'integer', true, '' , true , true  );
        $this->AddCampo( 'cod_organograma', 'integer', true, '' , true , true  );
        $this->AddCampo( 'cod_nivel'      , 'integer', true, '' , true , true  );
        $this->AddCampo( 'cod_entidade'   , 'integer', true, '' , true , true  );
        $this->AddCampo( 'exercicio'      , 'char'   , true, '4', true , true  );
        $this->AddCampo( 'unidade_gestora', 'integer', true, '' , false, false );

    }

    public function recuperaUnidadeGestora(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaUnidadeGestora", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaUnidadeGestora()
    {
        $stSql  = "\n SELECT unidade_gestora "
                 ."\n FROM tcmpa.unidade_orcamentaria "
                 ."\n WHERE exercicio = ".$this->getDado('exercicio')
                 ."\n   AND cod_entidade = ".$this->getDado('cod_entidade')
                 ."\n GROUP BY unidade_gestora"
                 ."\n ";

        return $stSql;
    }

    public function recuperaListagemOrgaosOrcamentarios(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaListagemOrgaosOrcamentarios", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaListagemOrgaosOrcamentarios()
    {
        $stSql  = "\n SELECT '020' AS tipo_registro"
                 ."\n      , unidade_orcamentaria.cod_orgao"
                 ."\n      , orgao.descricao"
                 ."\n      , unidade_orcamentaria.unidade_gestora"
                 ."\n      , '*' AS fim_registro"
                 ."\n "
                 ."\n   FROM tcmpa.unidade_orcamentaria"
                 ."\n      , organograma.orgao"
                 ."\n   WHERE unidade_orcamentaria.cod_orgao = orgao.cod_orgao"
                 ."\n     AND unidade_orcamentaria.cod_entidade = ".$this->getDado('cod_entidade')
                 ."\n ";

        return $stSql;
    }

}
