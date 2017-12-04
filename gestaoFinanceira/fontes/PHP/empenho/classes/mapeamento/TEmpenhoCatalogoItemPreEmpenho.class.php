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
    * Classe de mapeamento da tabela licitacao.convenio
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id $

    * Casos de uso: uc-03.05.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.convenio
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoCatalogoItemPreEmpenho extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TEmpenhoCatalogoItemPreEmpenho()
    {
        parent::Persistente();

        $this->setTabela("empenho.catalogo_item_pre_empenho");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_pre_empenho, num_item');

        $this->AddCampo( 'exercicio'      , 'char'   , true , '4', true , true );
        $this->AddCampo( 'cod_pre_empenho', 'integer', true , '' , true , true );
        $this->AddCampo( 'num_item'       , 'integer', true , '' , true , true );
        $this->AddCampo( 'cod_item'       , 'integer', false, '' , false, true );
        $this->AddCampo( 'cod_marca'      , 'integer', false, '' , false, true );
    }

    public function monta()
    {
        $stSql  = "";

        return $stSql;
    }

    public function recupera(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("monta",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
}
