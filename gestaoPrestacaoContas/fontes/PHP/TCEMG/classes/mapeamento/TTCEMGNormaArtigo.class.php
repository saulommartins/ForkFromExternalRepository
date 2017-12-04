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
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: LUCAS STEPHANOU

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGORestosPagar.class.php 56934 2014-01-08 19:46:44Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGNormaArtigo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGNormaArtigo()
    {
        parent::Persistente();

        $this->setTabela('tcemg.norma_artigo');
        $this->setCampoCod('cod_artigo');
        $this->setComplementoChave('');

        $this->AddCampo('cod_artigo', 'integer', true, '', true,  false);
        $this->AddCampo('exercicio', 'character', false, 4, true,  false);
        $this->AddCampo('cod_norma', 'integer', false, '', false,  true);
        $this->AddCampo('num_artigo', 'integer', false, '', false,  false);
        $this->AddCampo('descricao', 'varchar', false, '', false,  false);

        $this->setDado('exercicio', Sessao::getExercicio());
    }
    
    public function __destruct(){}

}
