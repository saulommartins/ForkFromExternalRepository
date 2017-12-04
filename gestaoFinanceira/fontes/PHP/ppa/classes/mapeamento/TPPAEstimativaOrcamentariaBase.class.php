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
 * Classe de mapeamento ppa.estimativa_orcamentaria_base
 * Data de Criação: 07/04/2009

 * @author Analista: Tonismar Bernardo
 * @author Desenvolvedor: Henrique Girardi dos Santos

 *  $Id:$

 */

class TPPAEstimativaOrcamentariaBase extends Persistente
{
    // Método construtor
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('ppa.estimativa_orcamentaria_base');
        $this->setCampoCod('cod_receita');

        $this->AddCampo('cod_receita'   , 'integer', true, ''  , true , false);
        $this->AddCampo('cod_estrutural', 'varchar', true, '30', false, false);
        $this->AddCampo('descricao'     , 'varchar', true, '80', false, false);
        $this->AddCampo('tipo'          , 'varchar', true, '1' , false, false);
    }

    public function montaRelacionamento()
    {

    }
}
