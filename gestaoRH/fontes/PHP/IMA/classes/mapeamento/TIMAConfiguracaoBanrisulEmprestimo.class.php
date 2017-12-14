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
/*
* @author     Desenvolvedor Cassiano de Vasconcellos Ferreira
* $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
* Classe de mapeamento para a tabela ima.configuracao_banrisul_emprestimo
*
* @category   Urbem
* @package    PackageName
* @author     Analista Fulano de Tal
* @author     Desenvolvedor Cassiano de Vasconcellos Ferreira
 */
class TIMAConfiguracaoBanrisulEmprestimo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TIMAConfiguracaoBanrisulEmprestimo()
    {
        parent::Persistente();
        $this->setTabela("ima.configuracao_banrisul_emprestimo");

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo('cod_evento'        ,'integer'       ,true  ,''    ,false ,'');

    }
}
?>
