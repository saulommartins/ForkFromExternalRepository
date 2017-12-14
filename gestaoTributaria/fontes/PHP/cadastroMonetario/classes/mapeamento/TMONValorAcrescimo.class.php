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
    * Classe de regra de negocio para MONETARIO.VALOR_ACRESCIMO
    * Data de Criacao: 04/08/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONValorAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.11
*/

/*
$Log$
Revision 1.2  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONValorAcrescimo extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TMONValorAcrescimo()
{
    parent::Persistente();
    $this->setTabela('monetario.valor_acrescimo');

    $this->setCampoCod('cod_acrescimo');
    $this->setComplementoChave('cod_tipo');

    $this->AddCampo('cod_acrescimo', 'integer', true, '', true, true);
    $this->AddCampo('cod_tipo', 'integer', true, '', true, true);
    $this->AddCampo('inicio_vigencia', 'date', true, '', true, false);
    $this->AddCampo('valor', 'numeric', true, '14,6', false, false);
}

}// fecha classe de mapeamento
