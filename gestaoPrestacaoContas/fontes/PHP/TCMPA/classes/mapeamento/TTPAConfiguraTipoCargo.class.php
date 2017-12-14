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
    * Classe de mapeamento da tabela TCMPA.CONFIGURA_TIPO_CARGO
    * Data de Criação: 02/06/2008

    * @author Analista: Gelson W. Golçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  TCMPA.LOTACAO
  * Data de Criação: 02/06/2008

  * @author Analista: Gelson W. Golçalves
  * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

*/
class TTPAConfiguraTipoCargo extends Persistente
{

    /**
      * Método Construtor
      * @access Private
    */
    public function TTPAConfiguraTipoCargo()
    {
        parent::Persistente();
        $this->setTabela('tcmpa.configura_tipo_cargo');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_cargo, cod_tipo');

        $this->AddCampo( 'cod_cargo'   , 'integer', true, '', true, false );
        $this->AddCampo( 'cod_tipo', 'integer', true, '', true, true  );
    }

    public function recuperaListagemConfiguraTipoCargo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaListagemConfiguraTipoCargo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaListagemConfiguraTipoCargo()
    {
        $stSQL .= " SELECT cod_cargo, cod_tipo
                      FROM tcmpa".
        getEntidade().".configura_tipo_cargo
                      where cod_cargo=".$this->getDado('cod_cargo')."
                        and cod_tipo=".$this->getDado('cod_tipo');

        return $stSQL;
    }

    public function excluirTipoCargo()
    {
        return $this->executaRecupera("montaExcluirTipoCargo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaExcluirTipoCargo()
    {
        $stSQL .= "DELETE from tcmpa".Sessao::getEntidade().".configura_tipo_cargo
                    where cod_cargo=".$this->getDado('cod_cargo');

        return $stSQL;
    }

}
