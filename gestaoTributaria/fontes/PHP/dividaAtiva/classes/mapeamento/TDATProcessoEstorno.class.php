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
    * Classe de mapeamento da tabela DIVIDA.PROCESSO_ESTORNO
    * Data de Criação: 02/08/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATProcessoEstorno.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2007/08/02 19:36:20  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATProcessoEstorno extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATProcessoEstorno()
    {
        parent::Persistente();
        $this->setTabela('divida.processo_estorno');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_inscricao,exercicio,cod_processo,ano_exercicio');

        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('cod_processo','integer',true,'',false,true);
        $this->AddCampo('ano_exercicio','varchar',true,'4',false,true);
    }

}// end of class

?>
