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

/*
 * Classe de mapeamento da tabela tceto.transferencia_tipo_transferencia
 *
 * @package SW2
 * @subpackage Mapeamento
 * @version $Id:$
 * @author Lisiane Morais
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCETOTransferenciaTipoTransferencia extends Persistente
{

    public function TTCETOTransferenciaTipoTransferencia()
    {
        parent::Persistente();
        $this->setTabela('tceto.transferencia_tipo_transferencia');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_lote, exercicio, cod_entidade, tipo');

        $this->AddCampo('cod_lote'                 , 'integer', true, ''  , true , true);
        $this->AddCampo('exercicio'                , 'char'   , true, '04', true , true);
        $this->AddCampo('cod_entidade'             , 'integer', true, ''  , true , true);
        $this->AddCampo('tipo'                     , 'char'   , true, '1' , true , true);
        $this->AddCampo('cod_tipo_transferencia'   , 'integer', true, ''  , false, true);
        $this->AddCampo('exercicio_empenho'        , 'char'   , false, '4' , false, true);
        $this->AddCampo('cod_empenho'              , 'intger' , false, ''  , false, true);
    }

}

?>