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
    * Classe de mapeamento da tabela TCMPA.TIPO_REMUNERACAO
    * Data de Criação: 21/05/2008

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Luiz Felipe P Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  TCMPA.LOTACAO
  * Data de Criação: 21/05/2008

  * @author Analista: Gelson W. Golçalves
  * @author Desenvolvedor: Luiz Felipe P Teixeira

*/
class TTPAConfiguraSituacaoFuncional extends Persistente
{

    /**
      * Método Construtor
      * @access Private
    */
    public function TTPAConfiguraSituacaoFuncional()
    {
        parent::Persistente();
        $this->setTabela('tcmpa'.Sessao::getEntidade().'.configura_situacao_funcional');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_situacao, cod_sub_divisao','cod_regime','cod_tipo','cod_cargo');

        $this->AddCampo( 'cod_situacao'    , 'integer', true, '', true, false );
        $this->AddCampo( 'cod_sub_divisao', 'integer', true, '', true, true  );
        $this->AddCampo( 'cod_regime', 'integer', true, '', true, true  );
        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, true  );
    }

    public function recuperaListagemConfiguraSituacaoFuncional(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaListagemConfiguraSituacaoFuncional",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaListagemConfiguraSituacaoFuncional()
    {
        $stSQL .= " SELECT cod_sub_divisao, cod_tipo, cod_situacao, cod_regime
                      FROM tcmpa".Sessao::getEntidade().".configura_situacao_funcional
                      where cod_regime=".$this->getDado('cod_regime')."
                      and cod_sub_divisao=".$this->getDado('cod_sub_divisao')."
                      and cod_tipo=".$this->getDado('cod_tipo')."
                      and cod_situacao=".$this->getDado('cod_situacao');

        return $stSQL;
    }

    public function deletaSituacaoFuncional()
    {
        return $this->executaRecupera("montaDeletaSituacaoFuncional",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaDeletaSituacaoFuncional()
    {
        $stSQL .= "DELETE from tcmpa".Sessao::getEntidade().".configura_situacao_funcional
                    where cod_regime=".$this->getDado('cod_regime')."
                      and cod_sub_divisao=".$this->getDado('cod_sub_divisao');

        return $stSQL;
    }
}
