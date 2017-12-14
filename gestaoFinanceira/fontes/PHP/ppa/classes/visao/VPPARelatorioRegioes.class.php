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
    * Classe de Visão de Relatório de Regiões
    * Data de Criação: 13/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.07
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
//require_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");

final class VPPARelatorioRegioes
{
    /**
        * Atributo que recebe o objeto da Regra de Negócio
        * @name $obController
    */
    private $obController;

    /**
        * Atributo que recebe um Array contendo os Mapeamentos.
        * @name $arMap
    */
    private $arMap;

    /**
        * Método Construtor da Classe
        * @param object $obNegocio
        * @return void
    */
    public function __construct($obNegocio)
    {
        $this->obController = $obNegocio;
    }

    /**
        * Método que encaminha para a Tela principal printar o Relatório de Regiões
        * @param array $arParam
        * @return void
    */
    public function encaminhaRelatorioRegioes($arParam)
    {
        $pgProg = "PRRelatorioRegioes.php?stAcao=gerarRelatorioRegioes";
        $pgProg.= "&inCodPPA=" . $arParam['inCodPPA'];
        $pgProg.= "&boDescricaoRegiao=" . $arParam['boDescricaoRegiao'];

        $return = sistemaLegado::alertaAviso($pgProg, '', "incluir", "aviso", Sessao::getId(), "../");

        return $return;
    }

    /**
        * Método que executa o Relatório na Tela Principal
        * @param array $arParam
        * @return void
    */
    public function gerarRelatorioRegioes($arParam)
    {
        if ($arParam['inCodPPA'] == "") {
            $cod_ppa = 0;
        } else {
            $cod_ppa = $arParam['inCodPPA'];
        }

        # FONTE NÃO UTILIZADO, REMOVER DO REPOSITÓRIO QUANDO POSSÍVEL.
        #$preview = new PreviewBirt(2, 43, 1);
        #$preview->setTitulo('Relatório do Birt');
        #$preview->setVersaoBirt('2.2.1');
        #//$preview->setExportaExcel(true);
        #$preview->addParametro("cod_ppa", $cod_ppa);
        #$preview->addParametro("desc_regiao",  $arParam['boDescricaoRegiao']);
        #return $preview->preview();
    }
}

?>
