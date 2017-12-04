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
    * Classe de mapeamento da tabela PPA.CONFIGURACAO_NORMA
    * Data de Criação: 26/09/2006

    * @author Analista:
    * @author Desenvolvedor: Leandro Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.09.01
*/

/*
$Log$
Revision 1.2  2007/06/21 15:28:44  leandro.zis
corrigido nome das tabelas nos comentarios

Revision 1.1  2007/06/05 14:38:11  leandro.zis
uc 02.09.01

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAConfiguracaoNorma extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAConfiguracaoNorma()
    {
        parent::Persistente();
        $this->setTabela('ppa.configuracao_norma');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_configuracao');

        $this->AddCampo('cod_configuracao', 'integer', true, '', true, 'TPPAConfiguracao');
        $this->AddCampo('cod_norma', 'integer', true, '', true, 'TNormasNorma');
    }

} // end of class
