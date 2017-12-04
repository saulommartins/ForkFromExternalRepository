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
    * Classe de mapeamento da tabela tipo pagamento
    * Data de Criação: 18/09/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: $
    $Name$
    $Author:  $
    $Date:  $

    * Casos de uso:
*/

/*
$Log$
Revision 1.2  2006/09/18 10:47:57  cako
implementação do uc-02.04.27, uc-02.04.26

Revision 1.1  2006/09/13 17:25:23  cako
implementaçao do uc-02.04.27

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTipoPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTipoPagamento()
{
    parent::Persistente();
    $this->setTabela("tceal.tipo_pagamento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote, cod_entidade, exercicio, tipo');

    $this->AddCampo('cod_lote'               , 'integer'  , true , ''  , true  , true  );
    $this->AddCampo('cod_entidade'       , 'integer'  , true , ''  , true  , true  );
    $this->AddCampo('exercicio'              , 'varchar'  , true , '04', true  , true  );
    $this->AddCampo('tipo'                      , 'char'      , true , '01', true  , true  );
    $this->AddCampo('tipo_pagamento'  , 'varchar'  , true , '15'  , false , true  );
    $this->AddCampo('descricao'            , 'varchar'  , true , '10'  , false , true  );
}

}
