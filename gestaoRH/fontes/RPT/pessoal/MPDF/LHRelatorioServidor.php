<div>
    <table class='border'>
        <thead>
            <tr class='border font_weight_bold text_align_center'>
                <th colspan=3>RELATÓRIO SERVIDOR</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<br/>
<?php
if (count($servidores ) > 0) {
    foreach ($servidores as $key => $servidor) {    
        if ($servidor['dados_spans'] || $servidor['dados_spans'] != '') {
    ?>
            <div id="dados_span">
                <table class="border background_color_cinza_c0c0c0">
                    <tbody>
                        <?php                   
                            echo "<tr class=\'border font_weight bold text_align_center\'>";
                            echo "<td class=\"font_weight_bold text_align_left\">".$servidor['dados_spans']['tipo'].": ".$servidor['dados_spans']['span']."</td>";
                            echo "</tr>";
                        ?>
                    </tbody>
                </table>
            </div>
            <br/>
    <?php
        }
    ?>
    <div id="dados_titulo">
        <table>
            <tbody>
                <?php
                    echo "<tr>";
                    echo "<td class=\"font_weight_bold text_align_left\" > MATRICULA: ".$servidor['dados_titulo']['matricula']." </td>";                        
                    echo "</tr>";            
                ?>
            </tbody>
        </table>
    </div>
    <br/>
    
    <?php if (!empty($servidor['dados_identificacao'])) {  ?>
    <div id="dados_identificacao">
        <table class='border' >
            <thead> 
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="2">Dados de Identificação</th>
                </tr>            
            </thead>
            <tbody>
                <tr>
                    <td width="60%">
                        <table >
                            <tbody>
                                <?php                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Data de Nascimento: </td>";
                                    echo "<td class=\"text_align_justify\"> ".$servidor['dados_identificacao']['dt_nascimento']."</td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Sexo: </td>";
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['sexo']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Escolaridade: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['escolaridade']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Nome do Pai: </td>";                                            
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['nome_pai']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Nome da Mãe: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['nome_mae']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Estado Civil: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['estado_civil']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Raça/Cor: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['raca']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Cid: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['cid']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Nacionalidade: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['nacionalidade']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Município de Naturalidade/UF: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['nom_municipio']."/".$servidor['dados_identificacao']['sigla_uf']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Endereço:  </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['endereco']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Complemento do Endereço: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['complemento']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Bairro:  </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['bairro']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> CEP:  </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['cep']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Cidade/UF:  </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['cidade']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Fone Resindencial / Celular:  </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['fone']." </td>";
                                    echo "</tr>";
                                    
                                    echo "<tr>";
                                    echo "<td class=\"text_align_left\" width=\"40%\"> Email: </td>";                        
                                    echo "<td class=\"text_align_justify\" width=\"40%\"> ".$servidor['dados_identificacao']['e_mail']." </td>";
                                    echo "</tr>";
                                ?>
                            </tbody>
                        </table>
                    </td>
                    <td class="text_align_center">
                        <table>
                            <tbody>
                                <?php  
                                    echo "<tr>";
                                    if ( $servidor['dados_titulo']['foto'] ){
                                        echo "<td ><img src=\"".$servidor['dados_titulo']['foto']."\" width=\"30mm\" height=\"40mm\"/> </td>";
                                    }
                                    echo "</tr>";
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
    <?php if (!empty($servidor['dados_documentacao'])) {  ?>
    <div id="dados_documentacao">
        <table class='border'>
            <thead> 
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="5">Dados de Documentação</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        echo "<tr>";
                        echo "<td class=\"text_align_left\"              > CPF:                 ".$servidor['dados_documentacao']['cpf']."           </td>";
                        echo "<td class=\"text_align_left\"              > RG:                  ".$servidor['dados_documentacao']['rg']."            </td>";
                        echo "<td class=\"text_align_left\"              > Data Emissão RG:     ".$servidor['dados_documentacao']['dt_emissao_rg']." </td>";
                        echo "<td class=\"text_align_left\" colspan=\"2\"> Órgão Emissor RG/UF: ".$servidor['dados_documentacao']['orgao_emissor']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\"> PIS/PASEP:          ".$servidor['dados_documentacao']['pis_pasep']."          </td>";
                        echo "<td class=\"text_align_left\"> Cadastro Pis/Pasep: ".$servidor['dados_documentacao']['cadastro_pis_pasep']." </td>";
                        echo "<td class=\"text_align_left\"> Título Eleitor:     ".$servidor['dados_documentacao']['titulo_eleitor']."     </td>";
                        echo "<td class=\"text_align_left\"> Zona:               ".$servidor['dados_documentacao']['zona']."               </td>";
                        echo "<td class=\"text_align_left\"> Seção:              ".$servidor['dados_documentacao']['secao']."              </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\"              > Número CNH:    ".$servidor['dados_documentacao']['num_cnh']."         </td>";
                        echo "<td class=\"text_align_left\"              > Categoria CNH: ".$servidor['dados_documentacao']['categoria_cnh']."   </td>";
                        echo "<td class=\"text_align_left\" colspan=\"3\"> Validade CNH:  ".$servidor['dados_documentacao']['dt_validade_cnh']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\"              > CTPS:               ".$servidor['dados_documentacao']['num_ctps']."           </td>";
                        echo "<td class=\"text_align_left\"              > Série:              ".$servidor['dados_documentacao']['serie_ctps']."         </td>";
                        echo "<td class=\"text_align_left\"              > Data Emissão:       ".$servidor['dados_documentacao']['dt_emissao_ctps']."    </td>";
                        echo "<td class=\"text_align_left\" colspan=\"2\"> Orgão/UF Expedidor: ".$servidor['dados_documentacao']['orgao_emissao_ctps']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" colspan=\"5\"> Certificado Reservista: ".$servidor['dados_documentacao']['certificado_reservista']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" colspan=\"2\"> Conselho Profissional: ".$servidor['dados_documentacao']['conselho_profissional']." </td>";
                        echo "<td class=\"text_align_left\"              > Número:                ".$servidor['dados_documentacao']['num_conselho']."          </td>";
                        echo "<td class=\"text_align_left\" colspan=\"2\"> Data Validade:         ".$servidor['dados_documentacao']['dt_validade_conselho']."  </td>";
                        echo "</tr>";
                    ?>            
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
    <?php if (!empty($servidor['dados_contratuais'])) {  ?>
    <div id="dados_contratuais">
        <table class='border'>
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="2">Informações Contratuais</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Situação : </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['situacao']."</td>";                    
                        echo "</tr>";
                        
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Data Nomeação : </td>";
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['dt_nomeacao']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Portaria de Nomeção : </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['portaria']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Data Posse : </td>";                                            
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['dt_posse']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Data Admissão: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['dt_admissao']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Data Rescisão/Exoneração: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['dt_rescisao']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Motivo: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['motivo']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Regime / Subdivisão Cargo: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['regime_subdivisao_cargo']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Cargo / Especialidade : </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['cargo_especialidade']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Regime / Subdivisão Função: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['regime_subdivisao_funcao']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Função / Especialidade: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['funcao_especialidade']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Última Alteração Função: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['dt_alteracao_funcao']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Tipo Admissão: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['tipo_admissao']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Vínculo Empregatício: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['vinculo']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Categoria: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['categoria']." </td>";
                        echo "</tr>";
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Validade Exame Médico: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_contratuais']['dt_validade_exame']." </td>";
                        echo "</tr>";
                    ?>
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
    <?php if (!empty($servidor['dados_salariais'])) {  ?>
    <div id="dados_salariais">
        <table class='border'>
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="2">Informações Salariais</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Carga Horária Mensal: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_salariais']['horas_mensais']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Carga Horária Semanal: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_salariais']['horas_semanais']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Padrão Salarial: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_salariais']['padrao_salarial']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Data Início Progressão: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_salariais']['dt_inicio_progressao']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Progressão: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_salariais']['progressao']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Salário/Vencimento Base: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_salariais']['salario']." </td>";
                        echo "</tr>";                 
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Tipo de Pagamento: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_salariais']['tipo_pagamento']." </td>";
                        echo "</tr>";
                    ?>
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
 <!--
    <div style="page-break-after: always"></div>
    -->
    <?php if (!empty($servidor['dados_bancarios'])) {  ?>
    <div id="dados_bancarios">
        <table class='border'>
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="2">Informações Bancárias</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Forma de Pagamento: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_bancarios']['forma_pagamento']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Banco: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_bancarios']['banco']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Agência: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_bancarios']['agencia']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Conta: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_bancarios']['num_conta']." </td>";
                        echo "</tr>";                   
                    ?>
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
    <?php if (!empty($servidor['dados_lotacao'])) {  ?>
    <div id="dados_lotacao">
        <table class='border'>
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="2">Informações de Localização</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Lotação: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_lotacao']['lotacao']." </td>";
                        echo "</tr>";                   
    
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Local de Trabalho: </td>";                        
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_lotacao']['local']." </td>";
                        echo "</tr>";                   
                    ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
    <br/>
    
    <?php if (!empty($servidor['dados_previdencia'])) {  ?>
    <div id="dados_previdencia">
        <table class='border'>
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="2">Informações de Previdência</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        echo "<tr>";
                        echo "<td class=\"text_align_left\" width=\"25%\"> Regime de Previdência Oficial: </td>";
                        echo "<td class=\"text_align_left\"> ".$servidor['dados_previdencia']['regime_previdencia']." </td>";
                        echo "</tr>";                   
                    ?>
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
    <?php if (!empty($servidor['dados_ferias'])) {  ?>
    <div id="dados_ferias">
        <table class='border'>
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold" colspan="9">Informações de Férias</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        echo "<tr>";
                        echo "<td class=\"border text_align_center font_weight_bold\" width=\"8%\"> Nro                </td>";
                        echo "<td class=\"border text_align_center font_weight_bold\" > Período Aquisitivo </td>";
                        echo "<td class=\"border text_align_center\"> Período Gozo       </td>";
                        echo "<td class=\"border text_align_center\"> Faltas             </td>";
                        echo "<td class=\"border text_align_center\"> Férias             </td>";
                        echo "<td class=\"border text_align_center\"> Abono              </td>";
                        echo "<td class=\"border text_align_center\"> Mês Pagto          </td>";
                        echo "<td class=\"border text_align_center\"> Folha              </td>";
                        echo "<td class=\"border text_align_center\"> Somente 1/3        </td>";
                        echo "</tr>";
                        
                        foreach ($servidor['dados_ferias'] as $ferias) {
                            echo "<tr>";
                            echo "<td class=\"border text_align_center font_weight_bold\"> ".$ferias['nro']."                </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['periodo_aquisitivo']." </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['periodo_gozo']."       </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['faltas']."             </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['ferias']."             </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['abono']."              </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['mes_pagamento']."      </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['folha']."              </td>";
                            echo "<td class=\"border text_align_center\"> ".$ferias['somente_13']."         </td>";
                            echo "</tr>"; 
                        }
                    ?>
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
    <?php if (!empty($servidor['dados_atributos'])) {  ?>
    <div id="dados_atributos">
        <table class='border'>
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold">Outras Informações – Atributos Dinâmicos</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        foreach ($servidor['dados_atributos'] as $atributos) {
                            echo "<tr>";
                            echo "<td class=\"text_align_left\" width=\"25%\"> ".$atributos['atributo']." </td>";                                            
                            echo "</tr>";                   
                        }
                    ?>
    
            </tbody>
        </table>
    </div>
    <?php } ?>
    <br/>
    
    <?php if (!empty($servidor['dados_dependentes'])) {  ?>
    <div id="dados_dependentes">
        <table >
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold">Dependentes</th>
                </tr>            
            </thead>
            <tbody>
                    <?php                
                        foreach ($servidor['dados_dependentes'] as $dependentes) {
                            echo "<tr>";
                            echo "<td>";
                            echo "<table class='border' width=\"1000px\">";
    
                            echo "<tr>";
                            echo "<td class=\"text_align_left\" colspan=\"2\"> Nome: ".$dependentes['nome']." </td>";                                                                    
                            echo "</tr>";                   
    
                            echo "<tr>";
                            echo "<td class=\"text_align_left\" > Data Nascimento: ".$dependentes['dt_nascimento']." </td>";
                            echo "<td class=\"text_align_left\"> CID: ".$dependentes['cid_dependente']." </td>";
                            echo "</tr>";
    
                            echo "<tr>";
                            echo "<td class=\"text_align_left\" > Grau de Parentesco: ".$dependentes['grau_parentesco']." </td>";
                            echo "<td class=\"text_align_left\"> Sexo: ".$dependentes['sexo']." </td>";
                            echo "</tr>";
                        
                            echo "<tr>";
                            echo "<td class=\"text_align_left\"  colspan=\"2\"> Escolaridade: ".$dependentes['escolaridade']." </td>";                        
                            echo "</tr>";
    
                            echo "<tr>";
                            echo "<td class=\"text_align_left\" > Dependência Salário Família: ".$dependentes['dependente_sal_familia']." </td>";
                            echo "<td class=\"text_align_left\"> Limite Salário Família: ".$dependentes['limite_salario_familia']." </td>";
                            echo "</tr>";
    
                            echo "<tr>";
                            echo "<td class=\"text_align_left\" colspan=\"2\"> Dependência IRRF: ".$dependentes['dependencia_irrf']." </td>";                        
                            echo "</tr>";
    
                            echo "</table>";
                            echo "<hr>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
    
            </tbody>
        </table>
    </div>
    <br/>
    <?php } ?>
    
    <?php if (!empty($servidor['dados_assentamento'])) {  ?>
    <div id="dados_assentamento">
        <table >
            <thead>
                <tr>
                    <th class="border text_align_center font_weight_bold">Assentamentos</th>
                </tr>            
            </thead>
            <tbody>
                    <?php
                        foreach ($servidor['dados_assentamento'] as $assentamentos) {
    
                            echo "<tr>";
                            echo "<td>";
                            echo "<table class='border' width=\"1000px\">";
                            
                            echo "<tr>";
                            echo "<td width=\"100px\"> Classificação: </td>";                        
                            echo "<td > ".$assentamentos['classificacao']." </td>";
                            echo "</tr>";
                            
                            echo "<tr>";
                            echo "<td > Assentamento: </td>";                        
                            echo "<td > ".$assentamentos['assentamento']." </td>";
                            echo "</tr>";                   
                            
                            echo "<tr>";
                            echo "<td  > Período: </td>";                        
                            echo "<td > ".$assentamentos['periodo']." </td>";
                            echo "</tr>";
                            
                            echo "<tr>";
                            echo "<td  > Quantidade de Dias: </td>";                        
                            echo "<td > ".$assentamentos['dias']." </td>";
                            echo "</tr>";
                            
                            echo "<tr>";
                            echo "<td > Normas: </td>";                        
                            echo "<td > ".$assentamentos['norma']." </td>";
                            echo "</tr>";
                            
                            echo "<tr>";
                            echo "<td > Observações: </td>";                                                
                            echo "<td > ".$assentamentos['observacao']." </td>";
                            echo "</tr>";
                            
                            echo "</table>";
                            echo "<hr>";
                            echo "</td>";
                            echo "</tr>";
    
                            
                            
                        }
                    ?>            
            </tbody>
        </table>
    </div>

    <?php
        //quebra de pagina se houver mais servidores
        #if (array_key_exists(($key+1), $servidores)) {
        #    echo 'BOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOB';
        #    echo "<div style=\"page-break-after: always\"></div>";        
        #}
    }//IF ASSENTAMENTOS 
    ?>
    <br/>
    
    <?php
    }//FOREACH SERVIDORES
}
?>