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
class TTPATipoRemuneracaoEvento extends Persistente
{

    /**
      * Método Construtor
      * @access Private
    */
    public function TTPATipoRemuneracaoEvento()
    {
        parent::Persistente();
        $this->setTabela('tcmpa.tipo_remuneracao_evento');

        $this->setCampoCod('');
        $this->setComplementoChave('codigo, cod_evento');

        $this->AddCampo( 'codigo'    , 'integer', true, '4', true , true );
        $this->AddCampo( 'cod_evento', 'integer', true, '' , true , true );
    }

    public function recuperaTipoRemuneracaoEventoDescricao(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaTipoRemuneracaoEventoDescricao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaTipoRemuneracaoEventoDescricao()
    {
        $stSql  = "\n SELECT tipo_remuneracao_evento.codigo"
                 ."\n     ,  tipo_remuneracao_evento.cod_evento"
                 ."\n     ,  evento.descricao"
                 ."\n     ,  evento.codigo as codigoDescricao"
                 ."\n FROM tcmpa.tipo_remuneracao_evento"
                 ."\n INNER JOIN folhapagamento.evento"
                 ."\n         ON  evento.cod_evento = tipo_remuneracao_evento.cod_evento";

        return $stSql;
    }

}
