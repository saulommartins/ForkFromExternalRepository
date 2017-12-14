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
 * Classe de mapeamento da tabela tcepe.tipo_responsavel
 * Data de Criação: 17/10/2014
 * @author Desenvolvedor Evandro Melos
 * @package URBEM
 * @subpackage
 * $Id: TTCEPETipoResponsavel.class.php 60415 2014-10-20 12:16:42Z evandro $
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEPETipoResponsavel extends Persistente
{
    /**
     * Método Construtor da classe de mapeamento
     *
     * @return void
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela  ('tcepe.tipo_responsavel');

        $this->setCampoCod('cod_tipo');

        $this->AddCampo('cod_tipo'  , 'integer', true, ''   , true , false);
        $this->AddCampo('descricao' , 'varchar', true, '30' , false, false);
    }
}
