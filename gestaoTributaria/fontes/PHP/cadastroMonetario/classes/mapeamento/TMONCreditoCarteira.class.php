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
    * Classe de regra de negócio para MONETARIO.CREDITO_CARTEIRA
    * Data de Criação: 17/03/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONCreditoCarteira.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.10
*/

/*
$Log$
Revision 1.4  2006/12/04 09:59:59  fabio
Bug #7678#

Revision 1.3  2006/11/29 17:57:27  marson
Bug #7678#
Correção do setComplementoChave no TMONCreditoCarteira que possuia as colunas cod_carteira e cod_convenio que não fazem parte da PK.

Revision 1.2  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONCreditoCarteira extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONCreditoCarteira()
{
    parent::Persistente();
    $this->setTabela('monetario.credito_carteira');

    $this->setCampoCod('cod_credito');
    $this->setComplementoChave('cod_especie,cod_genero,cod_natureza,cod_credito');

    $this->AddCampo('cod_credito','INTEGER',true,'',true,false);
    $this->AddCampo('cod_natureza','INTEGER',true,'',true,true);
    $this->AddCampo('cod_genero','INTEGER',true,'',true,true);
    $this->AddCampo('cod_especie','INTEGER',true,'',true,true);
    $this->AddCampo('cod_convenio','INTEGER',true,'',false,true);
    $this->AddCampo('cod_carteira','INTEGER',true,'',false,true);
}

} // fecha classe
