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
    * Classe de mapeamento da tabela EMPENHO.ITEM_PRE_EMPENHO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TEmpenhoItemPreEmpenho.class.php 65141 2016-04-27 20:10:02Z evandro $

    * Casos de uso: uc-02.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TEmpenhoItemPreEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('empenho.item_pre_empenho');

        $this->setCampoCod('num_item');
        $this->setComplementoChave('cod_pre_empenho,exercicio');

        $this->AddCampo('cod_pre_empenho', 'integer', true , ''     , true , true   );
        $this->AddCampo('exercicio'      , 'char'   , true , '04'   , true , true   );
        $this->AddCampo('num_item'       , 'integer', true , ''     , true , false  );
        $this->AddCampo('cod_unidade'    , 'integer', true , ''     , false, true   );
        $this->AddCampo('cod_grandeza'   , 'integer', true , ''     , false, true   );
        $this->AddCampo('quantidade'     , 'numeric', true , '14.4' , false, false  );
        $this->AddCampo('nom_unidade'    , 'varchar', true , '80'   , false, false  );
        $this->AddCampo('sigla_unidade'  , 'varchar', true , '20'   , false, false  );
        $this->AddCampo('vl_total'       , 'numeric', true , '14.2' , false, false  );
        $this->AddCampo('nom_item'       , 'varchar', true , '160'  , false, false  );
        $this->AddCampo('complemento'    , 'text'   , true , ''     , false, false  );
        $this->AddCampo('cod_item'       , 'integer', false, ''     , false, true   );
        $this->AddCampo('cod_centro'     , 'integer', false, ''     , false, true   );
        $this->AddCampo('cod_marca'      , 'integer', false, ''     , false, true   );
    }
}
